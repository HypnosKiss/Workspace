<?php

namespace sales\coroutine;

/**
 * 系统调度
 * Class SystemCall
 * @package sales\coroutine
 */
class SystemCall {
	protected $callback;

	public function __construct(callable $callback){
		$this->callback = $callback;
	}

	public function __invoke(Task $task, Scheduler $scheduler){
		$callback = $this->callback;
		return $callback($task, $scheduler);
	}
}