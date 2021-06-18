<?php


class CoSocket {

	protected $masterCoSocket  = null;
	public    $socket;
	protected $handleCallback;
	public    $streamPoolRead  = [];
	public    $streamPoolWrite = [];

	public function __construct($socket, CoSocket $master = null){
		$this->socket         = $socket;
		$this->masterCoSocket = $master ?? $this;
	}

	public function accept(){
		$isSelect = yield from $this->onRead();
		$acceptS  = null;
		if($isSelect && $as = stream_socket_accept($this->socket, 0)){
			$acceptS = new CoSocket($as, $this);
		}
		return $acceptS;
	}

	public function read($size){
		yield from $this->onRead();
		yield ($data = fread($this->socket, $size));
		return $data;
	}

	public function write($string): Generator{
		yield from $this->onWriter();
		yield fwrite($this->socket, $string);
	}

	public function close(){
		unset($this->masterCoSocket->streamPoolRead[(int)$this->socket], $this->masterCoSocket->streamPoolWrite[(int)$this->socket]);
		yield ($success = @fclose($this->socket));
		return $success;
	}

	public function onRead($timeout = null){
		$this->masterCoSocket->streamPoolRead[(int)$this->socket] = $this->socket;
		$pool                                                     = $this->masterCoSocket->streamPoolRead;
		$rSocks                                                   = [];
		$wSocks                                                   = $eSocks = null;
		foreach($pool as $item){
			$rSocks[] = $item;
		}
		yield ($num = stream_select($rSocks, $wSocks, $eSocks, $timeout));
		return $num;
	}

	public function onWriter($timeout = null){
		$this->masterCoSocket->streamPoolWrite[(int)$this->socket] = $this->socket;
		$pool                                                      = $this->masterCoSocket->streamPoolRead;
		$wSocks                                                    = [];
		$rSocks                                                    = $eSocks = null;
		foreach($pool as $item){
			$wSocks[] = $item;
		}
		yield ($num = stream_select($rSocks, $wSocks, $eSocks, $timeout));
		return $num;
	}

	public function onRequest(){
		/** @var self $socket */
		$socket = yield from $this->accept();
		if($socket === null){
			return false;
		}
		$data     = yield from $socket->read(8192);
		$response = call_user_func($this->handleCallback, $data);
		yield from $socket->write($response);
		return yield from $socket->close();
	}

	public static function start($port, callable $callback){
		echo "Starting server at port $port...\n";
		$socket = @stream_socket_server("tcp://0.0.0.0:$port", $errNo, $errStr);
		if(!$socket){
			throw new Exception($errStr, $errNo);
		}
		stream_set_blocking($socket, 0);
		$coSocket                 = new self($socket);
		$coSocket->handleCallback = $callback;
		function gen($coSocket){
			/** @var coSocket $coSocket */
			while(true){
				yield from $coSocket->onRequest();
			}
		}

		foreach(gen($coSocket) as $item){
			var_dump($item);
		}
	}

}

CoSocket::start(8000, function($data){
	return <<<RES
HTTP/1.1 200 OK
Content-Type: text/plain
Content-Length: 12
Connection: close

hello world!
RES;
});
