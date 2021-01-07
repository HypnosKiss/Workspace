<?php

include_once 'bootstrap.php';

use swoole\SwooleBase;

/**
 * 客户端
 * Class SwooleClient
 */
class SwooleClient extends SwooleBase {

	public const DEFAULT_IP        = '127.0.0.1';
	public const DEFAULT_PORT      = 9501;
	public const DEFAULT_TIMEOUT   = -1;
	public const DEFAULT_SOCK_FLAG = null;

	/** @var Swoole\Client $client */
	protected $client;
	protected $ip;
	protected $port;
	protected $timeout;
	protected $sock_flag;

	public function __construct($ip = null, $port = null, $timeout = null, $sock_flag = null, $autoContent = true){
		$this->client    = new Swoole\Client(SWOOLE_SOCK_TCP, SWOOLE_SOCK_SYNC);
		$this->ip        = $ip ?: self::DEFAULT_IP;
		$this->port      = $port ?: self::DEFAULT_PORT;
		$this->timeout   = $timeout ?: self::DEFAULT_TIMEOUT;
		$this->sock_flag = $sock_flag ?: self::DEFAULT_SOCK_FLAG;
		if($autoContent){
			$this->debug('auto content.');
			$this->connect();
		}
	}

	public function connect(){
		if(!$this->client->connect($this->ip, $this->port, $this->timeout)){
			$this->close();
			$this->client->connect($this->ip, $this->port, $this->timeout);
		}
		$this->debug('content success.');
	}

	public function send($data){
		if($this->client->isConnected()){
			$data = json_encode($data);
			$data = pack(PACKAGE_LENGTH_TYPE, strlen($data)).$data;
			if($this->client->send($data)){
				$this->debug('send successfully.');
				return true;
			}
			$this->debug(sprintf('SwooleSend Error: %s', $this->client->errCode));
			return true;
		}
		$this->debug('Swoole Server does not connected.');
	}


	public function recv(){
		if($this->client->isConnected()){
			$this->debug('recv Server Msg:'.$this->client->recv());
			return true;
		}
		$this->debug('Swoole Server does not connected.');
	}

	public function timer($ms = 1000){

		$this->debug('Set an interval clock timer.');
		//定时器
		swoole_timer_tick($ms,
			function(){
				$data    = mt_rand(1, 9);
				$package = pack(PACKAGE_LENGTH_TYPE, strlen($data)).$data;
				$this->client->send($package);
			});
	}

	public function close(){
		$this->debug('close.');
		$this->client->close(); //启用 SWOOLE_KEEP 长连接后，close 调用的第一个参数要设置为 true 表示强行销毁长连接 socket
	}
}

$data   = [
	'type'    => 'websocket',
	'content' => 'hello',
];
$client = new SwooleClient('127.0.0.1', 9501);
$client->send($data);
echo $client->recv();
$client->close();