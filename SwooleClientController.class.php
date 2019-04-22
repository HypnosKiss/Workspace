<?php
/**
 * @Desc
 * @Author develop41
 * @Email  qbtlixiang@qq.com
 * Created by PhpStorm
 * User: develop41
 * Date: 2018-11-13 18:04:03
 */

namespace Admin\Controller;

use swoole_client;

/**
 * @Desc
 * @Author develop41
 * @Email  qbtlixiang@qq.com
 * Created by PhpStorm
 * User: develop41
 * Date: 2018-11-08 19:15:48
 */
class SwooleClientController
{

    protected $client;

    protected $ip;

    protected $port;

    protected $timeout;

    protected $config = [
        'ip'      => '127.0.0.1',
        'port'    => 9002, #启动进程端口号
        'timeout' => 1,
    ];

    public function __construct($config = NULL)
    {

        $this->client = new swoole_client(SWOOLE_SOCK_TCP);//默认同步tcp客户端，添加参数SWOOLE_SOCK_ASYNC为异步
        #$this->client  = new swoole_client(SWOOLE_SOCK_TCP | SWOOLE_KEEP);//默认同步tcp客户端，添加参数SWOOLE_SOCK_ASYNC为异步
        $this->port    = $config === NULL ? $this->config['port'] : $config['port'];
        $this->ip      = $config === NULL ? $this->config['ip'] : $config['ip'];
        $this->timeout = $config === NULL ? $this->config['timeout'] : $config['timeout'];
        $this->connect();
    }

    public function connect()
    {

        if (!$this->client->connect($this->ip, $this->port, $this->timeout)) {
            #throw new \Think\Exception(sprintf('Swoole Error: %s', $this->client->errCode));
        }

    }

    public function send($data)
    {

        if ($this->client->isConnected()) {
            $data = json_encode($data);
            //包头(length:是包体长度)+包体
            $packge = pack('N', \strlen($data)) . $data;
            if ($this->client->send($packge)) {
                return TRUE;
            } else {
                #throw new \Think\Exception(sprintf('SwooleSend Error: %s', $this->client->errCode));
            }
        } else {
            #throw new \Think\Exception('Swoole Server does not connected.');
        }
    }

    public function recv()
    {

        if ($this->client->isConnected()) {
            echo '来自Server消息:' . $this->client->recv();
        } else {
            #throw new \Think\Exception('Swoole Server does not connected.');
        }
    }

    public function timer($intval = 1000)
    {

        //定时器维持心跳
        /*swoole_timer_tick($intval, function () {

            $num  = mt_rand(1, 9);
            $data = pack('N', \strlen($num)) . $num;
            $this->client->send($data);
        });*/
    }

    public function close()
    {

        #$this->client->close();
    }

}
