<?php

class  HttpServer
{

    public $http;

    public function __construct($config)
    {

        //全局对象
        $this->http = new \swoole\http\server('0.0.0.0', 9502);

        $this->http->set($config);

        //注册事件
        $this->http->on('WorkerStart', [$this, 'onWorkerStart']);
        $this->http->on('request', [$this, 'onRequest']);
        $this->http->start(); //全局生命周期
    }

    public function onRequest($request, $response)
    {

        if (isset($request->server)) {
            foreach ($request->server as $key => $value) {
                $_SERVER[strtoupper($key)] = $value;
            }
        }
        if (isset($request->header)) {
            foreach ($request->header as $key => $value) {
                $_SERVER[strtoupper($key)] = $value;
            }
        }
        //取舍
        if (isset($request->get)) {
            if (isset($request->get)) {
                foreach ($request->get as $key => $value) {
                    $_GET[strtoupper($key)] = $value;
                }
            }
        }
//        if(isset($request->post)) {
//            foreach ($request->post as $key=>$value){
//                $_SERVER[strtoupper($key)]=$value;
//            }
//        }
//
//        foreach ($request->cookie as $key=>$value){
//            $_SERVER[strtoupper($key)]=$value;
//        }
//        foreach ($request->file as $key=>$value){
//            $_SERVER[strtoupper($key)]=$value;
//        }
//        foreach ($request->server as $key=>$value){
//            $_SERVER[strtoupper($key)]=$value;
//        }

        if ($_SERVER['PATH_INFO'] == '/favicon.ico') {
            //$response->end(''); //响应客户端
            return;
        }

        //执行应用并响应
        ob_start();
        try {
            think\Container::get('app')->run()->send();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
        $res = ob_get_contents();
        ob_end_clean();
        $response->end($res); //响应客户端

    }

    public function onWorkerStart($server)
    {

        if (!$server->taskworker) { //判断不是taskworker才加载框架
            //加载基础文件(自动加载)
            require __DIR__ . '/../thinkphp/base.php';
        }

        $redis = new Redis();
        $redis->connect('127.0.0.1', 6379);
        $GLOBALS['redis'] = $redis;

    }

    //清除worker进程redis 事件  kill -USR1

}

$config = [
    'worker_num'         => 5,
    'max_request'        => 1000, //防备内存
    'package_max_length' => 1024 * 1024 * 10,
    'upload_tmp_dir'     => __DIR__ . '/upload'
];

new HttpServer($config);