<?php
/**
 * 同步upd
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/22 0022
 * Time: 12:13
 */

//创建Server对象，监听 127.0.0.1:9502端口，类型为SWOOLE_SOCK_UDP
$serv = new swoole_server("0.0.0.0", 9502, SWOOLE_PROCESS, SWOOLE_SOCK_UDP);

$serv->set(
    [
        'worker_num' => 2
    ]
);

//监听数据发送事件
$serv->on(
    'Packet', function ($serv, $data, $clientInfo) {
    //向IP地址为220.181.57.216主机的9502端口发送一个hello world字符串。
    $serv->sendto($clientInfo['address'], $clientInfo['port'], "服务端发送的udp数据");
    var_dump($data, $clientInfo);
}
);

//启动服务器
$serv->start();
