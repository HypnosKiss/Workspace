<?php

// 创建一个同步非阻塞TCP客户端
$client = new swoole_client(SWOOLE_SOCK_TCP, SWOOLE_SOCK_SYNC);


// 随后建立连接，连接失败直接退出并打印错误码
$client->connect('118.24.109.254', 9502,6) || exit("connect failed. Error: {$client->errCode}\n");

//向服务端发送数据
$client->send("我要连接服务器");

//从服务端接收数据
//$response = $client->recv();

// 输出接受到的数据
//echo $response . PHP_EOL;


// 关闭连接
$client->close();
echo '我能不能执行';

