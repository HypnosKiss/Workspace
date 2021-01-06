<?php

//register_shutdown_function(); //错误捕获

class JobServer
{

    private $serv;

    private $table;

    private $task_worker_num;

    public function __construct($config = [])
    {

        //全局对象
        $this->serv  = new \swoole\server('0.0.0.0', 9502);
        $this->table = new swoole\table(1024);//创建内存表
        // var_dump($this->table);
        // 一个字段是保存的是task_worker的id号,保存的是状态
        // 第一个为字段名字
        // 第二个参数类型支持 swoole_table::TYPE_INT,
        // swoole_table::TYPE_FLOAT,
        // swoole_table::TYPE_STRING
        // 第三个参数指定字符串字段的最大长度，单位为字节。字符串类型的字段必须指定$size
        // swoole_table::TYPE_INT默认为4个字节，可以设置1，2，4，8一共4种长度
        // swoole_table::TYPE_STRING设置后，设置的字符串不能超过此长度
        // swoole_table::TYPE_FLOAT会占用8个字节的内存
        $this->table->column('task_worker_id', swoole\table::TYPE_INT, 1);//内存表增加一列
        $this->table->column('status', swoole\table::TYPE_INT, 1);
        // 定义好表的结构后，执行create向操作系统申请内存，创建表
        $this->table->create(); //必须在创建子进程之前
        $this->serv->set($config);
        //task进程数
        $this->task_worker_num = $this->serv->setting['task_worker_num'];
        //注册事件回调
        $this->serv->on('connect', [$this, 'OnConnect']);
        $this->serv->on('Start', [$this, 'OnStart']);
        $this->serv->on('WorkerStart', [$this, 'onWorkerStart']);
        $this->serv->on('Receive', [$this, 'onReceive']);
        $this->serv->on('Task', [$this, 'onTask']);
        $this->serv->on('Finish', [$this, 'onFinish']);
        $this->serv->start(); //全局生命周期
    }

    // 插入task_worker个数行记录
    public function onStart($server)
    {

        //根据当前的task_worker个数,决定创建多少行记录,为0代表空闲,为1代表忙碌
        for ($i = 0; $i < $this->task_worker_num; $i++) {
            $this->table->set($i, ['task_worker_id' => $i, 'status' => 0]);
            echo '创建行数:' . $this->table->count() . PHP_EOL;
            //var_dump($this->table->get($i));
        }
    }

    public function onWorkerStart($server, $worker_id)
    {

        //var_dump($this->table->get('0'));
    }

    public function onConnect($server)
    {
        //var_dump($server);
        /*new test();
        foreach ($this->table as $row) {
            var_dump($row);
        }*/
    }

    // 接收到客户端消息触发回复（投递异步任务）
    public function onReceive($server)
    {
        //模拟数据
        $data = [];
        for ($i = 0; $i < 10000; $i++) {
            $data[$i] = ['id' => $i, 'name' => '很大的数据'];
        }
        //查询下task进程状态
        $task = [];
        foreach ($this->table as $row) {
            //status为0,证明当前是空闲的task
            if ($row['status'] == 0) {
                $task[]['task_worker_id'] = $row['task_worker_id'];
            }
        }
        var_dump($task);
        $taskCount = count($task);
        //其中有几个是空闲
        if ($taskCount > 0 && $taskCount != $this->task_worker_num) {
            $count = count($data); //数据量没有变
            $data  = array_chunk($data, ceil($count / $taskCount)); //数据/task空闲状态id
            foreach ($data as $k => $v) {
                $v['task_worker_id'] = $k; //保存当前任务,来自于哪个task_worker_id
                $server->task($v, $task[$k]['task_worker_id']); //得到具体是哪个worker是空闲的
                echo '###########我是空闲状态的投递############' . PHP_EOL;
            }
        } else {
            // 都是忙碌状态,都是空闲状态,平均投递
            // 平均分配给相应的task进程
            // 数据平均分割成5份,交给5个工作进程
            $count = count($data);
            $data  = array_chunk($data, ceil($count / $this->task_worker_num));
            // (0-(task_worker_num-1)) 投递的时候的区间
            foreach ($data as $k => $v) {
                $v['task_worker_id'] = $k; //保存当前任务,来自于哪个task_worker_id
                $server->task($v, $k); //投递任务.到某个task进程当中
                echo '+++++++我是忙碌状态的投递+++++++++' . PHP_EOL;
            }
        }
        // sleep(5); //处理速度会变慢
    }

    // 接收异步投递的数据
    // 在task_worker进程内被调用。worker进程可以使用swoole_server_task函数向
    // task_worker进程投递新的任务。当前的Task进程在调用onTask回调函数时会将进程状态切换为忙碌，
    // 这时将不再接收新的Task，当onTask函数返回时会将进程状态切换为空闲然后继续接收新的Task。
    public function onTask($server, $task_id, $src_worker_id, $data)
    {
        // $server->stats() 得到当前Server的活动TCP连接数，启动时间，accpet/close的总次数等信息。
        echo '当前排队的task任务数量:' . $server->stats()['tasking_num'] . PHP_EOL;
        echo "消息任务编号为:{$task_id},来自于worker:{$src_worker_id}\n";

        //更新某个task进程状态,忙碌状态（自增1）
        //$this->table->incr($data['task_worker_id'], 'status');
        //if ($this->table->get($data['task_worker_id']['status'] == 0)) {
            echo '#######我是空闲状态######' . PHP_EOL;
            $this->table->set($data['task_worker_id'], ['status' => 1]);
            //var_dump($this->table->get($data['task_worker_id']));
            $rand = mt_rand(1, 10);
            sleep($rand); //阻塞10秒,模拟数据操作
            /*try {
                throw  new Exception('1');
            } catch (\Exception $e) {
                $server->sendMessage($e->getMessage(), $src_worker_id);
                //通知worker进程,重新投递,记录错误日志
            }*/
            //$server->finish(['task_worker_id'=>$data['task_worker_id']]);
            return ['task_worker_id' => $data['task_worker_id']];
        /*} else {
            echo '++++++++++++忙碌状态+++++++++' . PHP_EOL;
        }*/
    }

    // 接收 onTask 返回的消息进行处理
    // 当worker进程投递的任务在task_worker中完成时，
    // task进程会通过swoole_server->finish()或return方法将任务处理的结果发送给worker进程。
    public function onFinish($server, $task_id, $data)
    {
        echo PHP_EOL . '当前排队的task_worker已完成任务数量:' . $server->stats()['tasking_num'] . PHP_EOL;
        echo "消息任务编号为:{$task_id}" . PHP_EOL;
        // 注意事项,错误捕获
        // 处理完成将某个task进程状态更改为空闲的状态（自减1）
        //$this->table->decr($data['task_worker_id'], 'status');
        $this->table->set($data['task_worker_id'], ['status' => 0]);
        //var_dump($this->table->get($data['task_worker_id']));
    }

    public function onPipeMessage($server, $src_worker_id, $message)
    {

        // 记录错误,记录日志.重新投递
        $server->task($message, $src_worker_id);
    }

}

// 设置配置信息
$config = [
    'worker_num'               => 2,
    'reactor_num'              => 3,
    'task_worker_num'          => 3,
    'task_max_request'         => 5000,
    'max_request'              => 1024,
    // 心跳检测
    'heartbeat_check_interval' => 60,
    'heartbeat_idle_time'      => 120,
    //包头包体检测
    'open_length_check'        => true,
    'package_length_type'      => 'N',
    'package_body_offset'      => 4,
    'package_length_offset'    => 0,
    //'package_max_length'       => 1024 * 1024 * 3 //设置包的最长字节
];

new JobServer($config);

