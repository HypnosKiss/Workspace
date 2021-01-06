<?php

//创建Server对象，监听 0.0.0.0:9501端口
$serv = new swoole_server("0.0.0.0", 9501, SWOOLE_PROCESS, SWOOLE_SOCK_TCP);

//配置
$serv->set([
               'worker_num'      => 2, //设置工作进程
               'reactor_num'     => 6, //线程组个数,最大不得超过cpu*4
               'max_request'     => 1000,
               'task_worker_num' => 5,  //异步工作进程
               'package_max_length '=>1024*1024*3,//总的请求数据大小字节为单位
               //+++ 数据打包 +++
               /*'open_length_check'     => true,
               'package_length_type'   => 'N',//无符号、网络字节序、4字节
               'package_length_offset' => 0, //计算总长度
               'package_body_offset'   => 4,//包体位置*/

           ]);

//工作进程
$serv->on('WorkerStart', function ($serv, $worker_id) {
    //var_dump($worker_id);
});

//监听连接进入事件,有客户端连接进来的时候会触发
$serv->on('connect', function ($serv, $fd, $from_id) {
    //var_dump($serv,$from_id);
});

//监听数据接收事件,server接收到客户端的数据后，worker进程内触发该回调
$serv->on('receive', function ($serv, $fd, $from_id, $data) {

    //指定投放到哪一些task进程,默认task一次只会开启一个进程 ++ 模拟数据
    $data = [];
    for ($i = 0; $i < 10000; $i++) {
        $data[$i] = ['id' => $i, 'name' => '很大的数据'];
    }
    $serv->send($fd, '已经帮你处理任务了,请耐心等待');
    //数据平均分割成5份,交给5个工作进程
    $task_worker_num = 5;
    $count           = count($data);
    $data            = array_chunk($data, ceil($count / $task_worker_num));
    // (0-(task_worker_num-1)) 投递的时候的区间
    foreach ($data as $k => $v) {
        $v['src_task_id'] = $k; //保存当前任务,来自于哪个task_worker_id
        $serv->task($v, $k); //投递任务.到某个task进程当中
    }
    echo "我去执行同步任务了 bye\n";
});

//监听数据接收事件,server接收到客户端的数据后，worker进程内触发该回调
$serv->on('task', function ($serv, $task_id, $src_worker_id, $data) {

    //var_dump($src_worker_id);
    echo "消息任务:{$task_id}来自于worker:{$src_worker_id}\n";
    //echo "taskWorker进程当中接收".$data."\n";
    sleep(3);

    return ['sucess' => $data['src_task_id']]; //返回到woker进程,并且返回的woker进程,
    //谁投递就返回给谁,触发Finish

});

//task任务执行完毕,接收到数据了才会触发
$serv->on('Finish', function ($serv, $task_id, $data) {

    //echo "worker进程当中:".$data."\n";
    echo "task:{$task_id}--处理完成\n";

});

$serv->start();


