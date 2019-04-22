<?php
/**
 * Created by PhpStorm.
 * Auth: Linfeng
 * Date: 2018/11/27
 */

class Swoole
{
    private $ser;
    private $port;
    private $configure;
    private $onTask;
    private $onOpen;
    private $onClose;
    private $listen;
    private $guard;
    private $pidfile;

    public function __construct()
    {
        set_time_limit(0);
        $this->listen = [];
    }

    public function configure($configure)
    {
        $this->configure = $configure;
    }

    public function exec()
    {
        global $argv;
        if (php_sapi_name() != "cli"){
            exit("只能在命令行模式下运行");
        }
        $fileName = isset($argv[0])? $argv[0]: 'swoole';
        $index = strripos($fileName, '.');
        $pidFileName = substr($fileName, 0, $index);
        $this->pidfile = $pidFileName.'.pid';
        $command = isset($argv[1])? $argv[1]: '';
        $p2 = isset($argv[2])? $argv[2]: '';
        switch ($command){
            case 'reload':
                $this->reload();
                break;
            case 'restart':
                $this->restart();
                break;
            case 'start':
                if($p2 == '-d')
                    $this->guard = true;
                else
                    $this->guard = false;
                $this->start();
                break;
            case 'stop':
                $this->stop();
                break;
            default:
        }
    }

    private function reload()
    {
        if($this->isRun()){
            echo "\033[36m热重载-敬请期待\033[0m\n";
        }else{
            echo "\033[31m服务未启动\033[0m\n";
        }
    }

    private function start()
    {
        if($this->isRun()){
            echo "\033[32m服务已经在运行中\033[0m\n";
        }else{
            echo "\033[34m启动服务\033[0m\n";
            $this->startServer();
        }
    }

    private function restart()
    {
        if($this->isRun()) {
            echo "\033[34m重启服务\033[0m\n";
            $this->stopServer();
            $this->guard();
            $this->startServer();
        }else{
            echo "\033[31m服务未启动\033[0m\n";
        }
    }

    private function stop()
    {
        if($this->stopServer()){
            echo "\033[36m服务已成功停止\033[0m\n";
        }else{
            echo "\033[31m服务未启动\033[0m\n";
        }
    }

    public function setPort($port)
    {
        $this->port = $port;
    }

    public function on($on, $callback)
    {
        switch ($on){
            case 'onOpen':
                $this->onOpen = $callback;
                break;
            case 'onTask':
                $this->onTask = $callback;
                break;
            case 'onClose':
                $this->onClose = $callback;
                break;
        }
    }

    // Stop running
    private function stopServer(){
        if($pid = $this->isRun()){
            posix_kill($pid, SIGTERM);
            unlink($this->pidfile);
            return true;
        }else{
            return false;
        }
    }

    // Start running
    private function startServer()
    {
        if($this->guard){
            $this->guard();
        } else{

        }
        $this->ser = new \swoole_websocket_server("0.0.0.0", $this->port);
        $this->ser->set($this->configure);
        $this->ser->on('message', function ($ser, $frame){
            $ser->task([
                'fd'=>$frame->fd,
                'type'=>'websocket',
                'data'=>$frame->data
            ]);
        });
        $this->ser->on('open', function ($ser, $req) {
            call_user_func($this->onOpen, $ser, $req->fd);
        });
        $this->ser->on('close', function ($ser, $fd) {
            call_user_func($this->onClose, $ser, $fd);
        });
        $this->ser->on('task', [$this, 'Task']);
        $this->ser->on('finish', function () {

        });
        $this->ser->on('start', function (){
            $this->createPidFile();
        });
        foreach ($this->listen as $v){
            $tcp = $this->ser->listen('0.0.0.0', $v, SWOOLE_SOCK_TCP);
            $tcp->set([
                'open_eof_check '=>true,
                'package_eof '=>"\n"
            ]);
            $tcp->on('receive', function ($ser, $fd, $from_id, $data){
                $ser->task([
                    'fd'=>$fd,
                    'type'=>'tcp',
                    'data'=>$data
                ]);
            });
            $tcp->on('close', function ($ser, $fd) {
                call_user_func($this->onClose, $ser, $fd);
            });
            $tcp->on('connect', function ($ser, $fd) {
                call_user_func($this->onOpen, $ser, $fd);
            });
        }
        $this->ser->start();
    }

    public function signalHandler($signal)
    {

        echo "信号处理\n";
        var_dump($signal);
    }

    // Task
    public function Task($ser, $task_id, $from_id, $frame)
    {
        call_user_func($this->onTask, $ser, $frame['fd'], $frame['type'], $frame['data']);
    }

    // Daemon
    private function guard(){
        global $stdin, $stdout, $stderr;
        umask(0);
        if(pcntl_fork() != 0) exit();
        posix_setsid();
        if(pcntl_fork() != 0) exit();
        fclose(STDIN);
        fclose(STDOUT);
        fclose(STDERR);
        $stdin  = fopen('/dev/null', 'r');
        $stdout = fopen('/dev/null', 'a');
        $stderr = fopen('/dev/null', 'a');
    }

    private function createPidFile(){
        $pid = posix_getpid();
        file_put_contents($this->pidfile, $pid);
    }

    // Is running
    private function isRun(){
        if(file_exists($this->pidfile)){
            $pid = file_get_contents($this->pidfile);
            // PID存在，尝试向其进行通讯;
            if(posix_kill($pid, SIGUSR1) == false){
                unlink($this->pidfile);
                return false;
            }
            return $pid;
        }else{
            return false;
        }
    }

    // listen
    public function listen($port){
        $this->listen = [$port];
    }
    
}