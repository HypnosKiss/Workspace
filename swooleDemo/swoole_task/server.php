<?php

//创建Server对象，监听 0.0.0.0:9501端口
$serv = new swoole_server("0.0.0.0", 9501, SWOOLE_PROCESS, SWOOLE_SOCK_TCP);

//配置
$serv->set([
               'worker_num'            => 5, //设置工作进程
               'reactor_num'           => 6, //线程组个数,最大不得超过cpu*4
               'max_request'           => 1000,
               'task_worker_num'       => 5,  //异步工作进程
               'open_length_check'     => true,
               'package_length_type'   => 'N',
               'package_length_offset' => 0, //计算总长度
               'package_body_offset'   => 4,//包体位置
               'package_max_length'    => 1024 * 1024 * 3 //总的请求数据大小字节为单位
           ]);

//工作进程
$serv->on('WorkerStart', function ($serv, $worker_id) {
    //var_dump($worker_id);
});

//监听连接进入事件,有客户端连接进来的时候会触发
$serv->on('connect', function ($serv, $fd, $from_id) {

});

//监听数据接收事件,server接收到客户端的数据后，worker进程内触发该回调
$serv->on('receive', function ($serv, $fd, $from_id, $data) {

    //前台发送了指令,处理100W条数据
    $serv->task($data); //投递任务.到某个task进程当中

    //指定投放到哪一些task进程,默认task一次只会开启一个进程
    $serv->send($fd, '已经帮你处理任务了,请耐心等待');

    echo "我去执行同步任务了 bye\n";

});

//监听数据接收事件,server接收到客户端的数据后，worker进程内触发该回调
$serv->on('task', function ($serv, $task_id, $src_worker_id, $data) {

    //var_dump($serv->stats()); //服务器状态
    //echo '开始时间';
    $time_start = microtime();
    //echo "消息任务:{$task_id}来自于worker:{$src_worker_id}\n";
    //echo "taskWorker进程当中接收".$data."\n";
    sleep(5);
    //for ($i=0;$i<10000000;$i++){

    //}
    // echo '结束时间';
    //$serv->finsh('');
    return '完毕了';
});

//task任务执行完毕,接收到数据了才会触发
$serv->on('Finish', function ($serv, $task_id, $data) {

    //echo "worker进程当中:".$data."\n";
    //echo "task:{$task_id}--处理完成\n";

});

$serv->start();


