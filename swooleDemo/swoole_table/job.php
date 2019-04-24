<?php

$GLOBALS['test'] = '六星教育' . PHP_EOL;


class JobServer
{


    private $serv;

    private $test;

    public function __construct($config = [])
    {

        //全局对象
        $this->serv = new \swoole\server('0.0.0.0', 9502);

        $this->serv->set($config);

        $this->test='全局变量'.PHP_EOL;

        //注册事件
        $this->serv->on('connect', [$this, 'OnConnect']);
        $this->serv->on('Start', [$this, 'OnStart']);
        $this->serv->on('WorkerStart', [$this, 'onWorkerStart']);
        $this->serv->on('Receive', [$this, 'onReceive']);
        $this->serv->on('Task', [$this, 'onTask']);
        $this->serv->on('Finish', [$this, 'onFinish']);

        $this->serv->start(); //全局生命周期

    }

    public function onStart($server)
    {


        //$this->test='我在主进程当中修改了';
        echo 'master:' . $this->test;

        $GLOBALS['test'] = 'peter' . PHP_EOL;
    }

    public function onWorkerStart($server)
    {

        //echo 'Worker:'.$this->test;
        echo $GLOBALS['test'];

    }

    public function onConnect($server)
    {

        //new test();

    }

    public function onReceive($server)
    {

        //new test();
        $server->task('123'); //投递任务.到某个task进程当中
        sleep(2); //处理速度会变慢

    }

    public function onTask($server, $task_id, $src_worker_id, $data)
    {

        echo $GLOBALS['test'];

        echo 'task进程' . $this->test;
        echo '当前排队的task任务数量:' . $server->stats()['tasking_num'] . PHP_EOL;
        echo "消息任务编号为:{$task_id},来自于worker:{$src_worker_id}\n";
        sleep(10); //阻塞10秒
    }

    public function onFinish($server)
    {


    }

}

$config = [
    'worker_num'               => 2,
    'reactor_num'              => 2,
    'task_worker_num'          => 2,//异步工作进程
    'task_max_request'         => 5000,
    'max_request'              => 1024, // 心跳检测
    'heartbeat_check_interval' => 60,
    'heartbeat_idle_time'      => 130, //包头包体检测
    'open_length_check'        => true,
    'package_length_type'      => 'N',
    'package_body_offset'      => 4,
    'package_length_offset'    => 0,
    'package_max_length'       => 1024 * 1024 * 3 //设置包的最长字节
];

new JobServer($config);

