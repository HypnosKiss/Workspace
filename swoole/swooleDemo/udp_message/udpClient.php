<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/7 0007
 * Time: 下午 9:51
 */

$client = new swoole_client(SWOOLE_SOCK_UDP, SWOOLE_SOCK_SYNC);

//不计后果
var_dump($client->sendto('127.0.0.1', 9501,'udp数据'));
//从服务端接收数据
$response = $client->recv();

// 输出接受到的数据
echo $response . PHP_EOL;
