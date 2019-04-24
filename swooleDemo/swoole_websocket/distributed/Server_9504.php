<?php

class Server_9504{
    public  $server;
    public  function  __construct($config)
    {
        //全局对象
        $this->server=new \swoole\http\server('0.0.0.0',9502);

        $this->server->set($config);

        //注册事件
        $this->server->on('WorkerStart',[$this,'onWorkerStart']);
        $this->server->on('message',[$this,'onRequest']);
        $this->server->start(); //全局生命周期
    }

    public  function  onWorkerStart($server){

        //加载基础文件(自动加载)
        //require __DIR__ . '/../thinkphp/base.php';
        //$redis=new Redis();
       //$redis->connect('127.0.0.1',6379);


    }

}