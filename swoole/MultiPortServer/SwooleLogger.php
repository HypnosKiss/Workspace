<?php

namespace swoole;

/**
 * 日志记录器
 * Trait SwooleLogger
 * @method emergency(...$messages)
 * @method alert(...$messages)
 * @method critical(...$messages)
 * @method error(...$messages)
 * @method warning(...$messages)
 * @method notice(...$messages)
 * @method info(...$messages)
 * @method debug(...$messages)
 */
trait SwooleLogger {

	/**
	 * var_export in minimal format
	 * @param      $var
	 * @param bool $return
	 * @return mixed|string|null
	 */
	protected function var_export_min($var, $return = false){
		if(is_array($var)){
			$toImplode = [];
			foreach($var as $key => $value){
				$toImplode[] = var_export($key, true).'=>'.$this->var_export_min($value, true);
			}
			$code = 'array('.implode(',', $toImplode).')';
			if($return){
				return $code;
			}

			echo $code;
		}else{
			return var_export($var, $return);
		}
		return null;
	}

	/**
	 * 输出日志
	 * @param $messages
	 * @param $level
	 */
	protected function outputLog($messages, $level){
		foreach($messages as $k => $msg){
			$messages[$k] = is_scalar($msg) ? $msg : $this->var_export_min($msg, true);
		}
		$level   = strtoupper($level);
		$message = implode(' ', $messages);
		echo date('H:i:s m/d').' '.__CLASS__." [$level] ".$message.PHP_EOL;
	}

	/**
	 * call log method
	 * @param $level_method
	 * @param $messages
	 * @return mixed
	 * @method emergency(...$messages)
	 * @method alert(...$messages)
	 * @method critical(...$messages)
	 * @method error(...$messages)
	 * @method warning(...$messages)
	 * @method notice(...$messages)
	 * @method info(...$messages)
	 * @method debug(...$messages)
	 */
	final public function __call($level_method, $messages){
		$this->outputLog($messages, $level_method);
	}
}