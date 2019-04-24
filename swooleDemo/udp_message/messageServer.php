<?php
//创建Server对象，监听 0.0.0.0:9501端口
$serv = new swoole_server("0.0.0.0", 9501,SWOOLE_PROCESS,SWOOLE_SOCK_TCP);


//配置
$serv->set([
    'worker_num' => 5, //设置进程
]);


//只开启一个定时器

$serv->on('WorkerStart',function ($serv,$worker_id){

    //进程0启动的时候,开启一个定时器,10秒钟选取一次
    if($worker_id==0){
        $serv->after(10000,function () use($serv){
            //执行了选举逻辑,耗时1秒
            foreach ($serv->connection_list() as $fd){
                 $serv->send($fd,'某某竞选成功'.PHP_EOL);
            }
        });
    }

});



//监听连接进入事件,有客户端连接进来的时候会触发
$serv->on('connect', function ($serv, $fd) {
    echo "有新的客户端连接，连接标识为$fd" . PHP_EOL;
});



//监听数据接收事件,server接收到客户端的数据后，worker进程内触发该回调
$serv->on('receive', function ($serv, $fd, $from_id, $data) {
    //一次性定时器,开启一个客户端就会启动一个定时器,得到所有已经连接上服务器的客户端
    $serv->send($fd, "服务器给你发送消息了: ".$data);



});


//监听连接关闭事件,客服端关闭，或者服务器主动关闭
$serv->on('close', function ($serv, $fd) {
    echo "编号为{$fd}的客户端已经关闭.".PHP_EOL;
});


//启动服务器
$serv->start();



