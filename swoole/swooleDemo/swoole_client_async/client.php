<?php
//异步tcp客户端

$client = new swoole_client(SWOOLE_SOCK_TCP, SWOOLE_SOCK_ASYNC);


//连接服务端
$client->on("connect", function(swoole_client $cli) {

      $cli->send("异步客户端数据"); //任务

});

//接收到服务端发送的消息时触发的
$client->on('receive', function ($cli, $data) {
      echo $data;
});


$client->on('error', function ($cli) {

});


//监听连接关闭事件,客服端关闭，或者服务器主动关闭
$client->on('close', function ($cli) {
});


//先绑定事件之后随后建立连接，连接失败直接退出并打印错误码
$client->connect('127.0.0.1', 9501) || exit("connect failed. Error: {$client->errCode}\n");

//打包

//定时器维持心跳
swoole_timer_tick(3000,function() use($client){
     //最好还是打包,解决粘包问题

     //pack();
     $client->send(mt_rand(1,9));
});

