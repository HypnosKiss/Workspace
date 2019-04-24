<?php

$server = new swoole_websocket_server('0.0.0.0', 1025);
$server->set([
                 'worker_num'  => 4,   //工作进程数量
                 'daemonize'   => 1, //是否作为守护进程
                 'log_file'    => './swoole.log', //指定swoole错误日志文件
                 'max_request' => 1000, //设置worker进程的最大任务数
                 //表示每60秒遍历一次，一个连接如果600秒内未向服务器发送任何数据，此连接将被强制关闭
                 /*'heartbeat_idle_time' => 600,
                 'heartbeat_check_interval' => 60,*/
             ]);
$server->on('open', function (swoole_websocket_server $server, $request) {

    echo "服务器:客户端{$request->fd}成功连接 IP为{$request->fd}\n";
});

$server->on('close', function ($ser, $fd) {

    echo "客户端 {$fd} 已关闭\n";
});

$server->on('message', function (swoole_websocket_server $server, swoole_websocket_frame $frame) {

    $_fd  = $frame->fd;//当前连接的唯一编号
    $msg  = $frame->data;
    $data = explode(',', $msg);
    /*start_time 服务器启动的时间
    connection_num 当前连接的数量
    accept_count 接受了多少个连接
    close_count 关闭的连接数量
    tasking_num 当前正在排队的任务数*/
    $stats = $server->stats();
    if ($data[1]) {//登录聊天室发送通知
        $info = "欢迎{$data[0]}进入聊天室，当前在线人数为{$stats['connection_num']}人";
        //给所有用户推送消息
        foreach ($server->connections as $fd) {
            if ($server->exist($fd)) {
                if ($_fd != $fd) {
                    $server->push($fd, $info);//给连接的客户端发送消息
                } else {
                    $server->push($fd, '登录成功');//给连接的客户端发送消息
                }
            }
        }
    } else {
        //给所有用户推送消息
        foreach ($server->connections as $fd) {
            if ($server->exist($fd)) {
                $info = $frame->data;
                if ($_fd !== $fd) {
                    $server->push($fd, $info);//给连接的客户端发送消息
                }
            }
        }
    }

});

$server->on('request', function ($request, $response) {

    // 接收http请求从get获取message参数的值，给用户推送
    // $this->server->connections 遍历所有websocket连接用户的fd，给所有用户推送
    foreach ($this->server->connections as $fd) {
        $this->server->push($fd, $request->get['message']);
    }
});

$server->start();