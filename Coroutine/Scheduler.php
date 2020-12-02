<?php

namespace sales\coroutine;

use Generator;
use SplQueue;

/**
 * 调度器
 * Class Scheduler
 * @package sales\coroutine
 */
class Scheduler {

	protected $maxTaskId = 0;
	protected $taskMap   = []; // taskId => task
	protected $taskQueue;

	/**
	 * 任务队列
	 * Scheduler constructor.
	 */
	public function __construct(){
		$this->taskQueue = new SplQueue();
	}

	/**
	 * 添加生成器任务
	 * @param \Generator $coroutine
	 * @return int
	 */
	public function addTask(Generator $coroutine){
		$tid                 = ++$this->maxTaskId;
		$task                = new Task($tid, $coroutine);
		$this->taskMap[$tid] = $task;
		$this->schedule($task);
		return $tid;
	}

	/**
	 * 杀死任务
	 * @param $tid
	 * @return bool
	 */
	public function killTask($tid){
		if(!isset($this->taskMap[$tid])){
			return false;
		}
		unset($this->taskMap[$tid]);
		foreach($this->taskQueue as $i => $task){
			if($task->getTaskId() === $tid){
				unset($this->taskQueue[$i]);
				break;
			}
		}
		return true;
	}

	/**
	 * 任务调度(把任务放进队列)
	 * @param \sales\coroutine\Task $task
	 */
	public function schedule(Task $task){
		$this->taskQueue->enqueue($task);
	}

	/**
	 * 启动协程任务
	 */
	public function run(){
		while(!$this->taskQueue->isEmpty()){
			/** @var \sales\coroutine\Task $task */
			$task   = $this->taskQueue->dequeue();
			$retVal = $task->run();
			if($retVal instanceof SystemCall){
				$retVal($task, $this);
				continue;
			}
			if($task->isFinished()){
				unset($this->taskMap[$task->getTaskId()]);
			}else{
				$this->schedule($task);
			}
		}
	}
}