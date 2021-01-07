<?php

$port          = 9501;
$address       = '127.0.0.1';
$remote_socket = "tcp://:$address:$port";

// $fp  = fsockopen("tcp://$address", $port);//使用 fsockopen 打开tcp连接句柄
// $msg = "fsockopen send message";
// fwrite($fp, $msg);//向句柄中写入数据

// $i=0;
// $ret = "";
// //循环遍历获取句柄中的数据，其中 feof() 判断文件指针是否指到文件末尾
// while(!feof($fp)){
// 	stream_set_timeout($fp, 2);
// 	$ret .= fgets($fp, 128);
// 	echo ++$i;
// }
// //关闭句柄
// fclose($fp);
// echo $ret;


$st     = "socket send message";
$length = strlen($st);
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);//创建tcp套接字
socket_connect($socket, $address, $port);              //连接tcp
$s = socket_write($socket, $st, $length);              //向打开的套集字写入数据（发送数据）
echo socket_read($socket, 8190);                       //从套接字中获取服务器发送来的数据
socket_close($socket);//关闭连接