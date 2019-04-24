<?php

//异步tcp客户端


$client = new swoole_client(SWOOLE_SOCK_TCP, SWOOLE_SOCK_ASYNC);


//自行做一下
$client->set([

]);


//连接服务端
$client->on("connect", function(swoole_client $cli) {

    /*$data='异步客户端数据';
    //包头(length:是包体长度)+包体

    $packge=pack('N',strlen($data)).$data;
    //echo strlen($data);

    //var_dump($packge);
    //连续发送5条数据
    for ($i=0;$i<5;$i++){
        $cli->send($packge);
    }*/

    $cli->send('测试情况');

});

//接收到服务端发送的消息时触发的
$client->on('receive', function ($cli, $data) {
      //粘包的问题作业
      echo $data.'|';
});


$client->on('error', function ($cli) {
});


//监听连接关闭事件,客服端关闭，或者服务器主动关闭
$client->on('close', function ($cli) {

});


//先绑定事件之后随后建立连接，连接失败直接退出并打印错误码
$client->connect('127.0.0.1', 9501) || exit("connect failed. Error: {$client->errCode}\n");

