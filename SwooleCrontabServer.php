<?php

/**
 * @Desc   Swoole异步任务
 * @Author develop41
 * @Email  qbtlixiang@qq.com
 * @Class  SwooleCrontabServer
 */
class SwooleCrontabServer
{

    public    $server;

    public    $db;

    protected $type          = 'android';

    protected $app_key       = '9cff4ed396e859554875589f';

    protected $master_secret = '84fb1cbff0bec1f149e1f7fa';

    /**
     * @Desc 初始化服务配置
     * SwooleCrontabServer constructor.
     * @param $config
     */
    public function __construct($config)
    {

        //全局对象
        $this->server = new swoole\server('0.0.0.0', $config['port']);
        $this->server->set($config);
        //注册事件
        $this->server->on('WorkerStart', [$this, 'onWorkerStart']);
        $this->server->on('connect', [$this, 'onConnect']);
        $this->server->on('receive', [$this, 'onReceive']);
        $this->server->on('task', [$this, 'onTask']);
        $this->server->on('finish', [$this, 'onFinish']);
        $this->server->on('close', [$this, 'onClose']);
        $this->server->start(); //全局生命周期
        $this->db = $this->connectPdo();
    }

    /**
     * @Desc   客户端连接触发
     * @Author develop41
     * @Email  qbtlixiang@qq.com
     * @param $server
     * @param $fd
     * @param $reactorId
     */
    public function onConnect($server, $fd, $reactorId)
    {

        #swoole_timer::after(10000, $this->onClose($server, $fd, $reactorId));
    }

    /**
     * @Desc   工作进程启动时触发
     * @Author develop41
     * @Email  qbtlixiang@qq.com
     * @param $server
     * @param $worker_id
     */
    public function onWorkerStart($server, $worker_id)
    {

        try {
            require_once 'require_once.php';
            if (!$server->taskworker) {
                if ($worker_id === 0) {
                    #$this->db = self::mysql();
                    swoole\Timer::tick(1000, function () {
                        shell_exec('find / -name "jpush.log" -size +10k -exec rm {}  \;');
                        shell_exec('find / -name "swoole.log" -size +3000k -exec rm {}  \;');
                        $time = date('Hi');#定时执行任务
                        if ($time === '1810') {
                            shell_exec('lnmp php-fpm reload');
                            #shell_exec('find / -name "*.log" -size +3000k -exec rm {}  \;');
                        }
                    });
                }
            }
        } catch (\Exception $e) {
            printf($e->getMessage());
        }
    }

    /**
     * @Desc   接收到tcp请求的时候会触发(投递异步任务)
     * @Author develop41
     * @Email  qbtlixiang@qq.com
     * @param $server
     * @param $fd
     * @param $from_id
     * @param $data
     */
    public function onReceive($server, $fd, $from_id, $data)
    {

        //得到包体长度
        $len  = unpack('N', $data)[1];
        $body = substr($data, -$len);//去除二进制数据之后,不要包头的数据
        //投递异步任务
        $task_id = $server->task($body);
        #向客户端发送数据
        $server->send($fd, "投递异步任务成功Task_id:" . $task_id);
    }

    /**
     * @Desc   异步任务处理
     * @Author develop41
     * @Email  qbtlixiang@qq.com
     * @param $server
     * @param $task_id
     * @param $from_id
     * @param $data
     */
    public function onTask($server, $task_id, $from_id, $data)
    {

        //返回任务执行的结果
        $data = json_decode($data, TRUE);
        if (!empty($data)) {
            switch ($data['task_type']) {
                case 'createCrontab':#
                    createCrontab($server, $data);
                    break;
                case 'push':#
                    $this->push($data);
                    break;
                case 'reload':
                    $server->reload();
                    break;
                default:
                    break;
            }
        }
    }

    /**
     * @Desc   异步任务完成通知
     * @Author develop41
     * @Email  qbtlixiang@qq.com
     * @param $server
     * @param $task_id
     * @param $data
     */
    public function onFinish($server, $task_id, $data) { }

    /**
     * @Desc   客户端关闭触发
     * @Author develop41
     * @Email  qbtlixiang@qq.com
     * @param $server
     * @param $fd
     * @param $reactorId
     */
    public function onClose($server, $fd, $reactorId) { }

    /**
     * @Desc   连接PDO
     * @Author develop41
     * @Email  qbtlixiang@qq.com
     * @return \PDO
     */
    public function connectPdo()
    {

        $db_config = [
            'dsn'      => 'mysql:host=127.0.0.1;dbname=wza;port=3306;charset=utf8',
            'host'     => '127.0.0.1',
            'port'     => '3306',
            'dbname'   => 'wza',
            'username' => 'root',
            'password' => 'a1b2c3wza!!',
            'charset'  => 'utf8',
        ];
        $options   = [
            PDO::ATTR_PERSISTENT => TRUE,
            #PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // 默认是PDO::FETCH_BOTH, 4
        ];
        try {
            return new PDO($db_config['dsn'], $db_config['username'], $db_config['password'], $options);
        } catch (PDOException $e) {
            die('数据库连接失败:' . $e->getMessage());
        }
    }

    /**
     * @Desc   连接mysql
     * @Author develop41
     * @Email  qbtlixiang@qq.com
     * @throws \Swoole\Mysql\Exception
     */
    public static function mysql()
    {

        $db     = new swoole_mysql();
        $server = [
            'host'     => '127.0.0.1',
            'port'     => 3306,
            'user'     => 'root',
            'password' => 'a1b2c3wza!!',
            'database' => 'wza',
            'charset'  => 'utf8', //指定字符集
            'timeout'  => 2,  // 可选：连接超时时间（非查询超时时间），默认为SW_MYSQL_CONNECT_TIMEOUT（1.0）
        ];
        $db->connect($server, function ($db, $r) {

            if ($r === FALSE) {
                var_dump($db->connect_errno, $db->connect_error);
                die;
            }
        });

        return $db;
    }
}

$config = [
    'port'                     => 9002, #启动进程端口号
    'worker_num'               => 1,
    #'daemonize'                => 1, //是否作为守护进程
    'max_request'              => 1000,
    'dispatch_mode'            => 1,#轮循模式，收到会轮循分配给每一个worker进程
    'task_worker_num'          => 8,#task进程的数量
    'task_max_request'         => 3000,#task最大请求数量-》重启
    'task_ipc_mode'            => 3,#使用消息队列通信，并设置为争抢模式
    'log_file'                 => './swoole.log', //指定swoole错误日志文件
    //打开包长检测特性。包长检测提供了固定包头+包体这种格式协议的解析。启用后，可以保证Worker进程onReceive每次都会收到一个完整的数据包。
    'open_length_check'        => TRUE, //长度值的类型，接受一个字符参数，与php的pack函数一致。目前swoole支持10种类型：
    'package_length_type'      => 'N',
    'package_length_offset'    => 0,//计算总长度
    'package_body_offset'      => 4,//包体位置
    'package_max_length'       => 1024 * 1024 * 2, //2M
    'heartbeat_check_interval' => 60,//轮询时间
    'heartbeat_idle_time'      => 600,//最大空闲时间
];

## 开启swoole服务
new SwooleCrontabServer($config);

