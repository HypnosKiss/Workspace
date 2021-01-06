<?php

//dns解析
//实现异步非阻塞
//Swoole\Async::dnsLookup("www.baidu.com", function ($domainName, $ip) {

//var_export($domainName, $ip);
$client = new swoole_http_client('120.77.156.0', 9500);
//设置请求头
//    $client->setHeaders([
//        'Accept' => 'text/html,application/xhtml+xml,application/xml',
//        'Accept-Encoding' => 'gzip',
//    ]);


//发送get请求
//    $client->get('/', function ($cli) {
//
//        var_dump($cli->body);
//    });


//  //自定义请求类型 PUT
//    $client->setMethod('PUT');
//    $client->setData(['name'=>'peter']);
//    $client->execute('/',function ($cli){
//        var_dump($cli);
//
//    });

//监听服务端给我们发送的数据
$client->on(
    'message', function ($cli, $frame) {
    var_dump($frame);
}
);

//websocket建立的是一个长连接
//发起WebSocket握手请求，并将连接升级为WebSocket。
$client->upgrade(
    '/', function ($cli) {

    $cli->push('六星教育');
}
);

//});

echo '是不是同步执行';