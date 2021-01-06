<?php


$client = new swoole_client(SWOOLE_SOCK_TCP, SWOOLE_SOCK_ASYNC);


$client->set(
    array(
        'socket_buffer_size' => 1024 * 1024 * 12, //2M缓存区
    )
);


//连接服务端
$client->on(
    "connect", function (swoole_client $cli) {

    //tcp消息边界,数据拆分,把大的数据拆分成小的数据
    $str = str_repeat('六星教育', 10000);
    //$str='123';
    $packge = pack('N', strlen($str)) . $str;

    $cli->send($packge);

}
);

//接收到服务端发送的消息时触发的
$client->on(
    'receive', function ($cli, $data) {
    //echo $data;
}
);


$client->on(
    'error', function ($cli) {
}
);


//监听连接关闭事件,客服端关闭，或者服务器主动关闭
$client->on(
    'close', function ($cli) {

}
);

//先绑定事件之后随后建立连接，连接失败直接退出并打印错误码
$client->connect('127.0.0.1', 9501) || exit("connect failed. Error: {$client->errCode}\n");

