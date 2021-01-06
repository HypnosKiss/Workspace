<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/8
 * Time: 20:26
 */
//创建Server对象，监听 0.0.0.0:9502端口
$serv = new swoole_server("0.0.0.0", 9502);

$serv->set([
    'worker_num' => 2, //设置进程
]);


//监听连接进入事件,有客户端连接进来的时候会触发
$serv->on('connect', function ($serv, $fd) {
    echo "有新的客户端连接，连接标识为$fd" . PHP_EOL;;
});


//监听数据接收事件,server接收到客户端的数据后，worker进程内触发该回调
$serv->on('receive', function ($serv, $fd, $from_id, $data) {
    $serv->send($fd, "服务器B给你返回数据". $data);
});

//监听连接关闭事件,客服端关闭，或者服务器主动关闭
$serv->on('close', function ($serv, $fd) {
    echo "编号为{$fd}的客户端已经关闭.".PHP_EOL;
});
//启动服务器
$serv->start();
