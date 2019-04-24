<?php


//根据端口跟ip地址划分业务,内网外网
$http=new \swoole\http\server('0.0.0.0',9502);


$port1=$http->addlistener('127.0.0.1',9503,SWOOLE_TCP);


//启用新协议
$port1->set([
    'package_max_length'=>1024*1024*10,
]);

$http->set([
    'package_max_length'=>1024*1024*10,
    'upload_tmp_dir'=>__DIR__.'/upload'
]);



//监听http协议
$http->on('request',function ($request,$response){

    var_dump('http');

//$response->end('<meta charset="UTF-8"><h2>六星教育</h2>'); //返回请求

});

//
////为端口1绑定回调
$port1->on('receive',function ($request,$response){

    var_dump('tcp');

//$response->end('<meta charset="UTF-8"><h2>六星教育</h2>'); //返回请求

});





$http->start();


