<?php

//创建Server对象，监听 0.0.0.0:9501端口
$serv = new swoole_server("0.0.0.0", 9500,SWOOLE_PROCESS,SWOOLE_SOCK_TCP);

//配置
$serv->set([
    'worker_num' =>5, //设置工作进程
    'reactor_num'=>6, //线程组个数,最大不得超过cpu*4
    'max_request'=>1000,
    //'daemonize'=>true,//设置守护进程
    //'log_file'=>__DIR__.'/server.log'//日志文件
]);


//



//主进程
$serv->on('start', function ($serv) {
       //var_dump($serv);
       //swoole_set_process_name("server:master");
       //echo "主进程启动". PHP_EOL;
});


//全局共享


//管理进程
$serv->on('managerStart', function ($serv) {
      //swoole_set_process_name("server:manage");
      echo "管理进程启动". PHP_EOL;
});



//工作进程
$serv->on('WorkerStart', function ($serv,$worker_id) {

     var_dump($serv->master_pid); //主进程id

     //include_once __DIR__.'/test.php';
     //var_dump(new test);

    //为什么需要每一个worker进程都加载一次?每个进程相互独立互不影响
     include_once __DIR__.'/auto.php';

    //swoole_set_process_name("server:Worker");
    //echo "工作进程启动". PHP_EOL;
});



//监听连接进入事件,有客户端连接进来的时候会触发
$serv->on('connect', function ($serv,$fd,$from_id) {
         //include_once __DIR__.'/test.php';

         //下面业务逻辑
         //$test=new test();
         //$test->index();





         echo "有新的客户端连接来自线程:{$from_id}，连接标识:$fd" . PHP_EOL;
});



//监听数据接收事件,server接收到客户端的数据后，worker进程内触发该回调
$serv->on('receive', function ($serv, $fd, $from_id, $data) {

         //通过client请求的形式,重启worker进程方法
          if($data=='reload'){
              $serv->reload();
          }

         echo "客户端来自线程:{$from_id}" . PHP_EOL;
});



//监听连接关闭事件,客服端关闭，或者服务器主动关闭
$serv->on('close', function ($serv, $fd) {
    echo "编号为{$fd}的客户端已经关闭.".PHP_EOL;
});

$serv->start();


