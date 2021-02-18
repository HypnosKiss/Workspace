<?php

include_once 'bootstrap.php';

use swoole\SwooleBase;

/**
 * Class SwooleService
 */
class SwooleService extends SwooleBase {

	/** @var Swoole\WebSocket\Server $websocket_server */
	public $websocket_server;

	/** @var Swoole\Server $websocket_server */
	public $tcp_server;

	/**
	 * 初始化 Swoole 服务
	 * SwooleService constructor.
	 * @param $config
	 * @param $port_list
	 */
	public function __construct($config, $port_list){
		//全局对象
		[$websocket_port, $tcp_port] = $port_list;
		$this->websocket_server = new Swoole\WebSocket\Server('0.0.0.0', $websocket_port);
		$this->websocket_server->set($config);
		//注册事件
		$this->websocket_server->on('workerStart', [$this, 'onWorkerStart']);
		$this->websocket_server->on('open', [$this, 'onOpen']);      //WebSocket 服务器
		$this->websocket_server->on('message', [$this, 'onMessage']);//WebSocket 服务器
		$this->websocket_server->on('request', [$this, 'onRequest']);//HTTP 服务器

		//配置 task_worker_num 后将会启用 task 功能。所以 Server 务必要注册 onTask、onFinish 2 个事件回调函数
		$this->websocket_server->on('task', [$this, 'onTask']);
		$this->websocket_server->on('finish', [$this, 'onFinish']);

		$this->websocket_server->on('close', [$this, 'onClose']);    //TCP 服务器 WebSocket 服务器
		$this->websocket_server->on('connect', [$this, 'onConnect']);//TCP 服务器

		// 您可以混合使用UDP/TCP，同时监听内网和外网端口，多端口监听参考 listen 小节。
		//多监听一个TCP端口，对外开启TCP服务，并设置TCP服务器的回调
		$this->tcp_server = $this->websocket_server->listen('0.0.0.0', $tcp_port, SWOOLE_SOCK_TCP);
		//默认新监听的端口 $tcp_port 会继承主服务器的设置，也是 HTTP 协议
		//需要调用 set 方法覆盖主服务器的设置
		$this->tcp_server->set([]);
		$this->tcp_server->on('receive', [$this, 'onReceive']);      //TCP 服务器
		$this->websocket_server->start();                            //全局生命周期
	}

	/**
	 * 此事件在 Worker 进程 / Task 进程 启动时发生，这里创建的对象可以在进程生命周期内使用。
	 * onWorkerStart/onStart 是并发执行的，没有先后顺序
	 * 可以通过 $server->taskworker 属性来判断当前是 Worker 进程还是 Task 进程
	 * 如果想使用 Reload 机制实现代码重载入，必须在 onWorkerStart 中 require 你的业务文件，而不是在文件头部。在 onWorkerStart 调用之前已包含的文件，不会重新载入代码。
	 * 可以将公用的、不易变的 php 文件放置到 onWorkerStart 之前。这样虽然不能重载入代码，但所有 Worker 是共享的，不需要额外的内存来保存这些数据。 onWorkerStart 之后的代码每个进程都需要在内存中保存一份
	 * @param \Swoole\WebSocket\Server $server
	 * @param                          $worker_id
	 */
	public function onWorkerStart(Swoole\WebSocket\Server $server, $worker_id){
		$this->debug('worker_id:', $worker_id, 'start.');
		require_once 'launch.inc.php';
		if(!$server->taskworker && $worker_id === 0){
			// var_dump($this->server);
			// $server->tick(100, function(){
			// 	$this->server->task(json_encode(['type' => 'task', 'content' => 'hello']));
			// });
		}
	}

	/**
	 * 监听WebSocket连接打开事件
	 * @param $server
	 * @param $request
	 */
	public function onOpen($server, $request){
		$this->debug("Client {$request->fd} Successfully connected IP is {$request->fd}");
	}

	/**
	 * @param $server
	 * @param $fd
	 */
	public function onConnect($server, $fd){
		$this->debug("Client {$fd} Successfully connected.");
	}

	/**
	 * @param $server
	 * @param $fd
	 */
	public function onClose($server, $fd){
		$this->debug("Client {$fd} closed");
	}

	/**
	 * websocket 消息 当服务器收到来自客户端的数据帧时会回调此函数
	 * @param $server
	 * @param $frame
	 */
	public function onMessage($server, $frame){
		$this->debug('receive websocket msg.');
		$data = $frame->data;
		$_fd  = $frame->fd; //当前连接的唯一编号
		switch($data){
			case 'start':
				$this->debug('server start');
				$server->start();
				break;
			case 'stop':
				$this->debug('server stop');
				$server->stop();
				break;
			case 'reload':
				$this->debug('server reload');
				$server->reload();
				break;
			case 'task':
				$this->debug('AsyncTask');
				$server->task($data);
				break;
			default:
				$this->debug('Push messages to all users.');
				foreach($server->connections as $fd){
					if($server->exist($fd)){
						$server->push($fd, $data); //给连接的客户端发送消息
					}
				}
				break;
		}
		$server->push($_fd, 'Successful operation');
	}

	/**
	 * 接收到 HTTP 请求的时候会触发
	 * @param \Swoole\Http\Request  $request
	 * @param \Swoole\Http\Response $response
	 */
	public function onRequest($request, $response){
		if(stripos($request->server['request_uri'], 'favicon.ico') === false){
			$this->websocket_server->after(1, function(){
				$this->websocket_server->task(json_encode(['type' => 'task', 'content' => 'hello']));
			});
			// $response->header("Content-Type", "text/plain");
			$response->write('<h1>Request received0</h1>');
			$response->write('<h2>hello my name is swoole http server</h2>');
			// $response->end('<h1>Request received</h1>');
			$this->debug("Request:", $request, 'Response:', $response);
		}
	}

	/**
	 * 接收到 UDP 数据包时回调此函数，发生在 worker 进程中 (监听数据接收事件)
	 * @param $server
	 * @param $data
	 * @param $clientInfo
	 */
	public function onPacket($server, $data, $clientInfo){
		$server->sendTo($clientInfo['address'], $clientInfo['port'], "Server ".$data);
		$this->debug($clientInfo);
	}

	/**
	 * 收到 TCP 协议的数据会回调此函数 (监听数据接收事件)
	 * @param \Swoole\Server $server
	 * @param int            $fd
	 * @param int            $reactorId
	 * @param string         $data
	 */
	public function onReceive($server, int $fd, int $reactorId, string $data){
		$this->debug("Dispatch AsyncTask.");
		//得到包体长度
		$len  = unpack(PACKAGE_LENGTH_TYPE, $data, 0)[1];
		$body = substr($data, -$len);//去除二进制数据之后,不要包头的数据
		//投递异步任务
		$task_id = $server->task($body);
		$this->debug("AsyncTaskID:$task_id", 'data:', $body);
		#向客户端发送数据
		$server->send($fd, "received data .");
	}

	/**
	 * 异步任务处理
	 * @param $server
	 * @param $task_id
	 * @param $from_id
	 * @param $data
	 * @return string
	 */
	public function onTask($server, $task_id, $from_id, $data){
		$this->debug("New AsyncTask[id=$task_id]");
		// self::sendWebsocketMessage($server, $data);
		//返回任务执行的结果
		$data = json_decode($data, true);
		if(!empty($data)){
			return 'task';
		}
		$server->push($from_id, 'Successful operation');
		//返回任务执行的结果
		$server->finish("$data -> OK");
	}

	/**
	 * 异步任务完成通知
	 * @param $server
	 * @param $task_id
	 * @param $data
	 */
	public function onFinish($server, $task_id, $data){
		$this->debug("AsyncTask[$task_id] Finish: $data");
	}
}

$port_list = [9500, 9501];
$file_path = sys_get_temp_dir().'/swoole/';
$xml_str   = '';
$row       = 1;
if(!file_exists($file_path) && !mkdir($file_path, 0777, true) && !is_dir($file_path)){
	throw new RuntimeException(sprintf('Directory "%s" was not created', $file_path));
}
$config = [
	'daemonize'                => false, //是否作为守护进程
	'log_file'                 => $file_path.'swoole.log', //指定swoole错误日志文件
	'worker_num'               => swoole_cpu_num(), //工作进程数量
	'max_request'              => swoole_cpu_num()*10000, //设置worker进程的最大任务数
	/*
	 *  1	轮循模式	收到会轮循分配给每一个 Worker 进程
		2	固定模式	根据连接的文件描述符分配 Worker。这样可以保证同一个连接发来的数据只会被同一个 Worker 处理
		3	抢占模式	主进程会根据 Worker 的忙闲状态选择投递，只会投递给处于闲置状态的 Worker
		4	IP 分配	根据客户端 IP 进行取模 hash，分配给一个固定的 Worker 进程。
			可以保证同一个来源 IP 的连接数据总会被分配到同一个 Worker 进程。算法为 ip2long(ClientIP) % worker_num
		5	UID 分配	需要用户代码中调用 Server->bind() 将一个连接绑定 1 个 uid。然后底层根据 UID 的值分配到不同的 Worker 进程。
			算法为 UID % worker_num，如果需要使用字符串作为 UID，可以使用 crc32(UID_STRING)
		7	stream 模式	空闲的 Worker 会 accept 连接，并接受 Reactor 的新请求
		使用建议 无状态 Server 可以使用 1 或 3，同步阻塞 Server 使用 3，异步非阻塞 Server 使用 1有状态使用 2、4、5
	 */
	'dispatch_mode'            => 2,// cannot set 'onConnect/onClose' event when using dispatch_mode=1/3/7
	// 计算方法 单个 task 的处理耗时，如 100ms，那一个进程 1 秒就可以处理 1/0.1=10 个 task
	// task 投递的速度，如每秒产生 2000 个 task 2000/10=200，需要设置 task_worker_num => 200，启用 200 个 Task 进程
	'task_worker_num'          => swoole_cpu_num()*10, //task进程的数量,Task 进程是同步阻塞的,最大值不得超过 swoole_cpu_num() * 1000
	'task_max_request'         => swoole_cpu_num()*10000, //设置 task 进程的最大任务数。【默认值：0】
	'task_ipc_mode'            => 3, //使用消息队列通信，并设置为争抢模式
	//表示每60秒遍历一次，一个连接如果600秒内未向服务器发送任何数据，此连接将被强制关闭
	'heartbeat_check_interval' => 60,//轮询时间
	'heartbeat_idle_time'      => 600,//最大空闲时间
	'open_length_check'        => true,//打开包长检测特性。包长检测提供了固定包头+包体这种格式协议的解析。启用后，可以保证Worker进程onReceive每次都会收到一个完整的数据包。
	'package_max_length'       => 1024*1024*2, //2M
	'package_length_type'      => PACKAGE_LENGTH_TYPE, //长度值的类型，接受一个字符参数，与 PHP 的 pack 函数一致。 see php pack() 无符号、网络字节序、4 字节
	'package_length_offset'    => PACKAGE_LENGTH_OFFSET,
	'package_body_offset'      => PACKAGE_BODY_OFFSET,
];

## 开启swoole服务 TCP telnet 127.0.0.1 9501  UDP netcat -u 127.0.0.1 9502
$swoole = new SwooleService($config, $port_list);
