<?php

namespace sales\coroutine;

use Generator;

/**
 * 任务
 * Class Task
 * @package sales\coroutine
 */
class Task {
	protected $taskId;
	protected $coroutine;
	protected $sendValue        = null;
	protected $beforeFirstYield = true;

	public function __construct($taskId, Generator $coroutine){
		$this->taskId    = $taskId;
		$this->coroutine = $coroutine;
	}

	public function getTaskId(){
		return $this->taskId;
	}

	public function setSendValue($sendValue){
		$this->sendValue = $sendValue;
	}

	public function run(){
		if($this->beforeFirstYield){
			$this->beforeFirstYield = false;
			return $this->coroutine->current();
		}

		$retVal          = $this->coroutine->send($this->sendValue);//向生成器中传入一个值
		$this->sendValue = null;
		return $retVal;
	}

	public function isFinished(){
		return !$this->coroutine->valid();
	}
}