<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/22 0022
 * Time: 13:50
 */

/**
 * 异步非阻塞客户端
 */
$client = new swoole_client(SWOOLE_SOCK_TCP, SWOOLE_SOCK_ASYNC);

$client->on(
    "connect", function (swoole_client $cli) {

    ## 打包数据
    $data    = '异步客户端数据';
    ## 包头+包体（pack函数进行打包）
    $package = pack('N', strlen($data)).$data; ## 打包完成
    ## 模拟粘包
    for ($i = 0; $i < 5; $i++) {
        $cli->send($package);
    }
}
);
//接收服务端消息触发
$client->on(
    "receive", function (swoole_client $cli, $data) {
    echo "Receive: $data";
    $cli->send(str_repeat('A', 1) . "\r\n");
    sleep(3);
}
);
$client->on(
    "error", function (swoole_client $cli) {
    echo "error\n";
}
);
$client->on(
    "close", function (swoole_client $cli) {
    echo "Connection close\n";
}
);
//先绑定事件才能建立连接
$client->connect('127.0.0.1', 9502);

//echo '我能不能执行';