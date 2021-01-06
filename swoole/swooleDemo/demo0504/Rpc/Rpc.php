<?php
//服务中心

class Rpc
{

    public    $server;

    public    $redis;

    protected $tcpServer;

    public function __construct($config)
    {

        //全局对象
        $this->server = new \swoole\websocket\server('0.0.0.0', 9800);
        $this->server->set($config);
        //注册事件
        $this->server->on('WorkerStart', [$this, 'onWorkerStart']);
        $this->server->on('message', [$this, 'onMessage']);
        $this->server->on('receive', [$this, 'onReceive']);
        $this->server->on('request', [$this, 'onRequest']);
        $this->server->on('close', [$this, 'onClose']);

        //监听tcp连接
        $this->tcpServer = $this->server->addlistener('0.0.0.0', '9801', SWOOLE_TCP);
        $this->tcpServer->set([
                                  'worker_num'         => 3,
                                  'package_max_length' => 1024 * 1024 * 10,
                                  'max_request'        => 3000,

                              ]);

        $this->server->start(); //全局生命周期
    }

    public function onWorkerStart($server)
    {

        //加载基础文件(自动加载)
        $this->redis = new \Redis();
        $this->redis->connect('127.0.0.1', 6379);
    }

    //客户端发送消息的时候触发
    public function onMessage($server, $frame)
    {

        $data = json_decode($frame->data, true);
        $fd   = $frame->fd;
        if (isset($data['method']) && $data['method'] == 'register') {  //当前客户端请求具体是做什么事情
            $service_key = 'service:' . $data['serviceName'];
            echo $service_key;
            $value = json_encode([
                                     'ip'   => $data['ip'],
                                     'port' => $data['port'],
                                     //'status'=>'active'
                                 ]);
            $res   = $this->redis->sAdd($service_key, $value); //添加进集合
            $redis = $this->redis;
            //利用定时器,检测代码服务端的存活状态
            if ($res) {
                $server->tick(3000, function ($id) use ($server, $service_key, $value, $redis, $fd) {

                    //不是存活的状态下
                    if (!$server->exist($fd)) {
                        //检测服务在不在redis当中,如果在就移除这个服务
                        if ($redis->SISMEMBER($service_key, $value)) {
                            //只是移除,如果需要可以更新状态
                            $redis->sRem($service_key, $value);
                        }
                        //获取集合当中的成员
                        var_dump($redis->sMembers($service_key));
                        //清除定时器
                        $server->clearTimer($id);
                    }
                });
            }

        } else {
            $server->push($frame->fd, '非法请求');
        }
        //var_dump('触发请求');
    }

    //接收到tcp请求的时候会触发
    public function onReceive($server, $fd, $reactor_id, $data)
    {

        /*[
            'service'=>'CartService',
            'action'=>'cart',
            'params'=>
        ];*/

        //哪个服务当中的哪个方法
//            $request=json_decode($data,true);
//            $serviceName=$request['service'];
//            $action=$request['action'];
//            $obj=new $serviceName();
//            $response=$obj->$action();
//            //客户端返回数据
//            $server->send($fd,$response);

        $this->send($data); //统一协议消息发送,服务分发

        $server->send($fd, '1231');
        //业务服务端提供服务
    }

    //接收http客户端,调用服务请求
    public function onRequest($request, $response)
    {

        //onRequest加载服务
        $data = [
            'service' => 'CartService',
            'action'  => 'cart',
            'params'  => ''
        ];
        //查询redis当中是否存在这个服务,并且服务是否可用,选取一个状态是比较好的一台机器来调用
        $this->send($data);

    }


    //http请求 request
    //客户端关闭的时候会触发
    public function onClose($server, $fd)
    {

        var_dump('客户端关闭', $fd);
    }

    /*发送一个服务调用请求*/
    public function send($data)
    {

        //发送请求注册中心
        $websocket = new HttpClient('118.24.109.254', 9503);

        //异步websocket请求
        $websocket->async_websocket(function ($cli) use ($data) {

            $cli->push($data);
        });

    }

}

spl_autoload_register(function ($class) {

    include_once dirname(__DIR__) . '/service/' . $class . '.php';
});
$config = [
    'worker_num'               => 6,
    'package_max_length'       => 1024 * 1024 * 10,
    'max_request'              => 3000,
    'heartbeat_idle_time'      => 5,//连接最大的空闲时间
    'heartbeat_check_interval' => 2 //定时检测在线列表
];

include_once dirname(__DIR__) . '/tool/HttpClient.php';

new Rpc($config);