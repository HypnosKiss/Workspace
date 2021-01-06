<?php

$server = new swoole_websocket_server('0.0.0.0', 9500);

// 心跳检测
$server->set(
    [
        'heartbeat_idle_time'      => 30,//最大空闲时间
        'heartbeat_check_interval' => 10,//10s 检测一次
    ]
);

//websocket连接触发
$server->on(
    'open', function (swoole_websocket_server $server, $request) {

    var_dump($request);
    //握手成功
    echo "server: handshake success with fd{$request->fd}\n";
}
);

//收到信息触发
$server->on(
    'message', function (swoole_websocket_server $server, $frame) {

    echo "receive from {$frame->fd}:{$frame->data},opcode:{$frame->opcode},fin:{$frame->finish}\n";
    //得到所有连接的客户端
    //getClientList仅可用于TCP客户端，UDP服务器需要自行保存客户端信息 *** 别名是connection_list
    //第1个参数是起始fd，第2个参数是每页取多少条，最大不得超过100。
    //用来遍历当前Server所有的客户端连接  **** 消息广播
    foreach ($server->connection_list() as $fd) {
        //检测websocket客户端是否有效
        if ($server->exist($fd)) {
            //发送给客户端
            $server->push($fd, "receive from {$fd}的消息", 2);
        } else {
            $server->close($fd); //关闭掉指定客户端
        }
    }
    //TCP连接迭代器 ***** 推荐使用
    /*foreach($server->connections as $fd)
    {
        $server->send($fd, "hello");
    }*/

}
);

$server->on(
    'receive', function ($server, $fd, $from_id, $data) {

    var_dump($data);
}
);
/*$server->on('request', function (swoole_http_request $request, swoole_http_response $response) {

    global $server;//调用外部的server
    // $server->connections 遍历所有websocket连接用户的fd，给所有用户推送
    foreach ($server->connections as $fd) {
        $server->push($fd, $request->get['message']);
    }
});*/

$server->on(
    'close', function ($ser, $fd) {

    echo "client {$fd} closed\n";
}
);

$server->start();