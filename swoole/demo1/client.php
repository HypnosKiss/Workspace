<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/22 0022
 * Time: 10:49
 */

/**
 * 同步阻塞客户端
 */
$client = new swoole_client(SWOOLE_SOCK_TCP,SWOOLE_SOCK_SYNC);
if (!$client->connect('127.0.0.1', 9502, -1)) {
    exit("connect failed. Error: {$client->errCode}\n");
}
$client->send("hello world\n");
echo $client->recv() . PHP_EOL;
$client->close();


