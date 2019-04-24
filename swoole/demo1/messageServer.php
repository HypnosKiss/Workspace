<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/22 0022
 * Time: 14:18
 */

try {
    $serv = new swoole_server('0.0.0.0', 9502, SWOOLE_PROCESS, SWOOLE_SOCK_TCP);

    $serv->set(
        array(
            'worker_num' => 2,   //工作进程数量
            'daemonize'  => false, //是否作为守护进程
        )
    );
    //有客户连接触发
    $serv->on(
        'connect', function ($serv, $fd) {
        echo "Client:{$fd}客户连接成功.\n";
    }
    );
    //只开启一个定时器
    $serv->on(
        'workerStart', function ($serv, $worker_id) {
        //var_dump($worker_id);
        if ($worker_id == 0) {
            $serv->after(
                10000, function () use ($serv) {
                foreach ($serv->connection_list() as $fd) {
                    $serv->send($fd, '服务器给你发送的消息: ' . '某某某' . PHP_EOL);
                }
            }
            );
        }
    }
    );

    //监听数据接收事件server接收到消息worker进程内触发
    $serv->on(
        'receive', function ($serv, $fd, $from_id, $data) {

        //定时器 得到所有连接服务器的客户端
        /*$serv->after(
            3000, function () use ($serv) {
            foreach ($serv->connection_list() as $fd) {
                $serv->send($fd, '服务器给你发送的消息: ' . '某某某' . PHP_EOL);
            }
        }
        );*/
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

