<?php
//创建Server对象，监听 0.0.0.0:9501端口
$serv = new swoole_server("0.0.0.0", 9501,SWOOLE_PROCESS,SWOOLE_SOCK_TCP);

//配置eof协议分包
//$serv->set([
//
//    'worker_num' => 2, //设置进程
//    'open_eof_check' => true, //打开EOF检测
//    'package_eof' => "\r\n", //设置EOF
//    'open_eof_split'=>true //自动进行边界分割
//]);
//

//固定包头跟包体协议
$serv->set([
    'open_length_check' => true,
    'package_length_type'=>'N',
    'package_length_offset'=>0, //计算总长度
    'package_body_offset'=>4,//包体位置
    'package_max_length'=>1024*1024 //总的请求数据大小字节为单位
]);




//监听连接进入事件,有客户端连接进来的时候会触发
$serv->on('connect', function ($serv, $fd) {

});


//监听数据接收事件,server接收到客户端的数据后，worker进程内触发该回调
$serv->on('receive', function ($serv, $fd, $from_id, $data) {



//    //应用层分包
//    $data=explode("\r\n", $data);
//    //echo $data.PHP_EOL;
//    foreach ($data as $v){
//        if(!$v) continue;
//        $serv->send($fd, "服务器给你发送消息了: ".$v.PHP_EOL);
//    }

    //缺点 数据当中不能出现eof标识
    //缺点 性能问题

     //得到包体长度
     $len=unpack('N',$data)[1];
     $body=substr($data,-$len);//去除二进制数据之后,不要包头的数据

     //打包之后的数据,又拼接了字符串 ()
     for ($i=0;$i<5;$i++){
         $serv->send($fd, "服务器给你发送消息了: ".$body.PHP_EOL);
     }



});


//监听连接关闭事件,客服端关闭，或者服务器主动关闭
$serv->on('close', function ($serv, $fd) {
});


//启动服务器
$serv->start();
