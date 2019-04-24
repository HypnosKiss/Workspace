<?php

//创建Server对象，监听 0.0.0.0:9501端口
$serv = new swoole_server("0.0.0.0", 9501,SWOOLE_PROCESS,SWOOLE_SOCK_UDP);


//配置
$serv->set([
    'worker_num' => 2, //设置进程
]);


//接收到udp数据的时候触发
$serv->on('Packet',function ($serv,$data,$clientInfo){
        //var_dump($data,$clientInfo);
        $serv->sendto($clientInfo['address'],$clientInfo['port'],'服务端udp数据包');
});

/*//监听数据接收事件,server接收到客户端的数据后，worker进程内触发该回调
$serv->on('receive', function ($serv, $fd, $from_id, $data) {
    var_dump($data);
    $serv->send($fd, "服务器给你发送消息了: ".$data);
});*/

//启动服务器
$serv->start();



