<?php

//创建Server对象，监听 0.0.0.0:9501端口
$serv = new swoole_server("0.0.0.0", 9502,SWOOLE_PROCESS,SWOOLE_SOCK_TCP);

$serv->addlistener('127.0.0.1',9503,SWOOLE_SOCK_TCP);


//配置
$serv->set(array(
    'worker_num' =>5, //设置工作进程
    'reactor_num'=>6, //线程组个数,最大不得超过cpu*4
    'max_request'=>1000,
    'package_max_length '=>1024*1024*3,
//    'open_length_check' => true,
//    'package_length_type'=>'N',
//    'package_length_offset'=>0, //计算总长度
//    'package_body_offset'=>4,//包体位置
//    'package_max_length'=>1024*1024*3 //总的请求数据大小字节为单位
));

//监听连接进入事件,有客户端连接进来的时候会触发
$serv->on('connect', function ($serv,$fd,$from_id) {

});


//监听数据接收事件,server接收到客户端的数据后，worker进程内触发该回调
$serv->on('receive', function ($serv, $fd, $from_id, $data) {

     $info=$serv->connection_info($fd);

     //区分内网外网
     if($info['server_port']){

     }


});





$serv->start();


