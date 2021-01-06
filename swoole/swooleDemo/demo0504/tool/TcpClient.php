<?php

class TcpClient
{

    private $client;

    private $ip;

    private $port;

    public function __construct($ip, $port)
    {

        $this->ip     = $ip;
        $this->port   = $port;
        $this->client = new swoole_client(SWOOLE_SOCK_TCP, SWOOLE_SOCK_SYNC);
    }

    //同步请求客户端
    public function sync($data)
    {

        if ($this->client->connect($this->ip, $this->port)) {
            $this->client->send($data); //发送
            $data = $this->client->recv();//接收数据
            //$this->client->close();
            return $data;
        }

    }

    //异步websocket请求
    public function async()
    {

    }

}