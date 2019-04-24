<?php

$client = new swoole_client(SWOOLE_SOCK_TCP, SWOOLE_SOCK_ASYNC);

//配置
$client->set([
                 'socket_buffer_size' => 1024 * 1024 * 2, //2M缓存区
             ]);

//连接服务端
$client->on("connect", function (swoole_client $cli) {

    $data = '异步客户端数据';
    //包头(length:是包体长度)+包体
    $packge = pack('N', strlen($data)) . $data;

    for ($i = 0;$i<2; $i++) {
        $cli->send($packge);
    }

});

//接收到服务端发送的消息时触发的
$client->on('receive', function ($cli, $data) {

    /*$data = '服务端触发数据';
    //包头(length:是包体长度)+包体
    $packge = pack('N', strlen($data)) . $data;

    for ($i = 0;$i<10; $i++) {
        $cli->send($packge);
    }*/
});

$client->on('error', function ($cli) {
});

//监听连接关闭事件,客服端关闭，或者服务器主动关闭
$client->on('close', function ($cli) {

});

//先绑定事件之后随后建立连接，连接失败直接退出并打印错误码
$client->connect('127.0.0.1', 9502) || exit("connect failed. Error: {$client->errCode}\n");

