<?php

#require_once 'Application/Common/Common/function.php';
#require_once 'Application/Api/Controller/AccessibilityController.class.php';

class SwooleCrontab
{

    public    $server;

    protected $type          = 'android';

    protected $app_key       = '9cff4ed396e859554875589f';

    protected $master_secret = '84fb1cbff0bec1f149e1f7fa';

    public function __construct($config)
    {

        //全局对象
        $this->server = new \swoole\websocket\server('0.0.0.0', $config['port']);
        $this->server->set($config);
        //注册事件
        $this->server->on('WorkerStart', [$this, 'onWorkerStart']);
        $this->server->on('message', [$this, 'onMessage']);
        $this->server->on('receive', [$this, 'onReceive']);
        $this->server->on('task', [$this, 'onTask']);
        $this->server->on('finish', [$this, 'onFinish']);
        $this->server->start(); //全局生命周期
    }

    //工作进程启动时触发
    public function onWorkerStart($server, $worker_id)
    {

        try {
            require_once 'require_once.php';
            if (!$server->taskworker) {
                if ($worker_id === 0) {
                    $count = curlGet('http://120.78.71.170/api.php/Accessibility/crontabCount', FALSE);
                    $count = $count > 100 ? 100 : $count;
                    for ($i = 0; $i < $count; $i++) {#根据任务个数开启多少定时器
                        $this->onTimer($server);
                    }
                    $server->tick(500, function () {

                        $this->redisDequeue('push');
                    });
                }
            }
        } catch (\Exception $e) {
            $this->onTimer($server);
            printf($e->getMessage());
        }
    }

    public function onTimer($server, $interval = 1000)
    {

        $server->tick($interval, function () {

            $url = 'http://120.78.71.170/api.php/Accessibility/push';
            curlGet($url, FALSE);
            /*$ch    = getCurlObject($url);
            asyncCurl($ch);*/
        });
    }

    public function onMessage($server, $frame)
    {

        $msg = $frame->data;
        switch ($msg) {
            case 'start':
                echo 'start-----' . date('Y-m-d H:i:s') . PHP_EOL;
                $server->start();
                break;
            case 'stop':
                echo 'stop-----' . date('Y-m-d H:i:s') . PHP_EOL;
                $server->stop();
                break;
            case 'reload':
                echo 'reload-----' . date('Y-m-d H:i:s') . PHP_EOL;
                $server->reload();
                break;
            default:
                $server->task($msg);
                break;
        }
        $server->push($frame->fd, '操作成功');
    }

    //接收到tcp请求的时候会触发
    public function onReceive($server, $fd, $from_id, $data)
    {

        //投递异步任务
        $task_id = $server->task($data);
        $server->send($fd, "request delivered and sleeping for 6 secs");
        #echo "Dispath AsyncTask: id=$task_id" . PHP_EOL;
    }

    public function onTask($server, $task_id, $from_id, $data)
    {

        #echo "New AsyncTask[id=$task_id]" . PHP_EOL;
        //返回任务执行的结果
        $data = json_decode($data, TRUE);
        if (!empty($data)) {
            return 'task';
        }
        //返回任务执行的结果
        #$server->finish("$data -> OK");
    }

    public function onFinish($server, $task_id, $data)
    {

        #echo "AsyncTask[$task_id] Finish: $data" . PHP_EOL;
    }

    /**
     * @Desc   极光推送异步队列
     * @Author develop41
     * @Email  qbtlixiang@qq.com
     * @param string $key
     * @return bool|int|mixed
     */
    public function redisDequeue($key = 'key')
    {

        require_once 'ThinkPHP/Library/Vendor/jpush/autoload.php';
        static $command = NULL;
        $content          = NULL;
        $msg_content      = NULL;
        $registration_ids = NULL;
        if ($command === NULL) {
            $command = getCommand();
        }
        $client = new \JPush\Client($this->app_key, $this->master_secret, './jpush.log', 3, 'BJ');
        $redis  = redis();//连接redis
        $lLen   = $redis->lLen($key);//获取该队列长度
        if ($lLen > 0) {
            for ($i = 0; $i < $lLen; $i++) {
                //用于移除并返回列表的最后一个元素。
                $list             = $redis->rPop($key);
                $param            = json_decode($list);//反序列化
                $content          = $param->data;
                $msg_content      = $param->command;
                $registration_ids = $param->registration_ids;
                try {
                    $data = [
                        'title'        => $command[$msg_content],//消息标题
                        'content_type' => 'text',//消息内容类型
                        'extras'       => object_to_array($content),//表示扩展字段，接受一个数组，自定义 Key/value 信息以供业务使用
                    ];
                    if (empty($registration_ids)) {//推送所有的设备
                        #$client->push()->setPlatform($this->type)->addAllAudience()->message($msg_content, $data)->send();
                    } else {
                        $res = $client->push()->setPlatform($this->type)->addAlias($registration_ids)->message($msg_content, $data)->send();
                        /*所有的 HTTP API Response Header 里都加了三项频率控制信息：
                         X-Rate-Limit-Quota：当前 AppKey 一个时间窗口内可调用次数
                         X-Rate-Limit-Remaining：当前时间窗口剩余的可用次数
                         X-Rate-Limit-Reset：距离时间窗口重置剩余的秒数*/
                        /*$seconds = $res['headers']['X-Rate-Limit-Reset'];
                        $nums    = $res['headers']['X-Rate-Limit-Remaining'];
                        if ($nums < 1) {
                            sleep($seconds);
                        }
                        if ($seconds < 1) {
                            sleep(1);
                        }*/
                        #sleep(1);
                        #var_dump($res);
                    }
                } catch (\JPush\Exceptions\APIConnectionException $e) {
                    #return $e->getCode();
                } catch (\JPush\Exceptions\APIRequestException $e) {
                    #return $e->getHttpCode();
                } catch (\Exception $e) {
                    #return $e->getCode();
                }
            }
        }
        #return FALSE;
    }

}

$config = [
    'port'            => 9001, #启动进程端口号
    'daemonize'       => 1, //是否作为守护进程
    'max_request'     => 1000,
    'dispatch_mode'   => 2,#保证同一个连接发来的数据只会被同一个worker处理
    'task_worker_num' => 1, //task进程的数量
    'task_ipc_mode'   => 3,            //使用消息队列通信，并设置为争抢模式
    'log_file'        => './swoole.log', //指定swoole错误日志文件
];

## 开启swoole服务
new SwooleCrontab($config);

