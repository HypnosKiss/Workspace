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

	/**
	 * 获取任务ID
	 * @return mixed
	 */
	public function getTaskId(){
		return $this->taskId;
	}

	/**
	 * 设置发送值
	 * @param $sendValue
	 */
	public function setSendValue($sendValue){
		$this->sendValue = $sendValue;
	}

	/**
	 * 执行任务
	 * @return mixed
	 */
	public function run(){
		if($this->beforeFirstYield){
			$this->beforeFirstYield = false;
			return $this->coroutine->current();
		}

		$retVal          = $this->coroutine->send($this->sendValue);//向生成器中传入一个值
		$this->sendValue = null;
		return $retVal;
	}

	/**
	 * 是否完成
	 * @return bool
	 */
	public function isFinished(){
		return !$this->coroutine->valid();
	}
}