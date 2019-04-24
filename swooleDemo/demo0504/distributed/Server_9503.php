<?php

class Server_9503
{

    public  $server;

    private $data;

    public function __construct($config, $data)
    {

        //全局对象
        $this->server = new \swoole\websocket\server('0.0.0.0', 9503);
        $this->data   = $data;

        $this->server->set($config);
        //注册事件
        $this->server->on('WorkerStart', [$this, 'onWorkerStart']);
        $this->server->on('message', [$this, 'onMessage']);
        $this->server->start(); //全局生命周期
    }

    //工作进程启动时触发
    public function onWorkerStart($server, $worker_id)
    {

        //服务只注册一次
        if ($worker_id == 0) {
            include_once dirname(__DIR__) . '/tool/HttpClient.php';
            $data           = $this->data;
            $data['method'] = 'register'; //代表当前是注册服务的方法

            //发送请求注册中心
            $websocket = new HttpClient('118.24.109.254', 9800);

            //异步websocket请求
            $websocket->async_websocket(function ($cli) use ($data) {

                $cli->push(json_encode($data)); //发送一个消息注册中心

                //定时器发送心跳包,维持存活状态
                swoole_timer_tick(2000, function ($id) use ($cli) {

                    $cli->push('', 9); //ping包,不会触发onMessage事件
                });

            });

        }

    }

    public function onMessage($server, $frame)
    {

        var_dump($frame);

    }

}

$config = [
    'worker_num'         => 6, //工作进程
    'package_max_length' => 1024 * 1024 * 10,
    'max_request'        => 3000
];

//购物车服务
$data = [
    'ip'          => '118.24.109.254',
    'port'        => 9503,
    'serviceName' => 'CartService'
];

new Server_9503($config, $data);

