<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/22 0022
 * Time: 11:18
 */

try {
    $serv = new swoole_server('0.0.0.0', 9502, SWOOLE_PROCESS, SWOOLE_SOCK_TCP);



    //设置固定数据、包头+包体
    $serv->set(
        [
            'daemonize'             => FALSE, //是否作为守护进程
            //打开包长检测特性。包长检测提供了固定包头+包体这种格式协议的解析。启用后，可以保证Worker进程onReceive每次都会收到一个完整的数据包。
            'open_length_check'     => TRUE, //长度值的类型，接受一个字符参数，与php的pack函数一致。目前swoole支持10种类型：
            'package_length_type'   => "N",
            'package_length_offset' => 0,//计算总长度
            'package_body_offset'   => 4,//包体位置
            'package_max_length'    => 1024 * 1024,//总数据请求大小
            'heartbeat_check_interval' => 3,//轮询时间
            'heartbeat_idle_time'      => 5//最大空闲时间
        ]
    );

    $serv->on(
        'connect', function ($serv, $fd) {

        echo "Client:Connect.\n" . $fd;
    }
    );

    $serv->on(
        'receive', function ($serv, $fd, $from_id, $data) {

        //var_dump($data);
        //sleep(5);
        //$serv->send($fd, 'Swoole: ' . $data);
        //得到包体长度
        $len  = unpack('N', $data)[1];
        $body = substr($data, -$len);
        $serv->send($fd, "服务器给你发送了消息：" . $body . PHP_EOL);
    }
    );

    $serv->on(
        'close', function ($serv, $fd) {

        echo "Client: Close.\n" . $fd;
    }
    );
    $serv->start();
} catch (Exception $e) {
    echo $e . PHP_EOL;
}
