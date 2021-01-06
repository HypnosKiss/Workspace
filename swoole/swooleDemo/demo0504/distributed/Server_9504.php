<?php

class Server_9504
{

    public $server;

    public function __construct($config)
    {

        //全局对象
        $this->server = new \swoole\websocket\server('0.0.0.0', 1026);
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

        }

    }

    public function onMessage($server)
    {

    }

}

$config = [
    'worker_num'         => 6, //工作进程
    'package_max_length' => 1024 * 1024 * 10,
    'max_request'        => 3000
];

//购物车服务

new Server_9504($config);