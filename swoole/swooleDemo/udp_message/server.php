<?php

/*
1、实例化Server对象
2、设置运行时参数
3、注册事件回调函数
4、启动服务器
*/
//创建Server对象，监听 0.0.0.0:9501端口
$serv = new swoole_server("0.0.0.0", 9501);

//服务端进行配置
$serv->set([
    'worker_num'=>2,//设置进程

]);


//监听客户端连接进入事件
$serv->on('connect', function ($server, $fd) {

      echo "Client: Connect.\n";
});

//监听数据接收事件
$serv->on('receive', function ($serv, $fd, $from_id, $data) {
    $serv->send($fd, "Server: ".$data);
});

//监听连接关闭事件
$serv->on('close', function ($serv, $fd) {
    echo "Client: Close.\n";
});

//启动服务器
$serv->start();

