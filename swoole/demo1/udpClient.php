<?php
/**
 * 同步upd
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/22 0022
 * Time: 11:39
 */

$client = new swoole_client(SWOOLE_SOCK_UDP, SWOOLE_SOCK_SYNC);

$client->sendto('127.0.0.1',9502,'UDP');

$response = $client->recv();

echo $response. PHP_EOL;