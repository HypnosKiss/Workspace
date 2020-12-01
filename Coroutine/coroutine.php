<?php

namespace sales\script\sales_info_sync;

use Generator;
use sales\coroutine\Scheduler;
use sales\coroutine\SystemCall;
use sales\coroutine\Task;


function addTask(Generator $coroutine){
	return new SystemCall(function(Task $task, Scheduler $scheduler) use ($coroutine){
		$task->setSendValue($scheduler->addTask($coroutine));
		$scheduler->schedule($task);
	});
}

function killTask($tid){
	return new SystemCall(function(Task $task, Scheduler $scheduler) use ($tid){
		$task->setSendValue($scheduler->killTask($tid));
		$scheduler->schedule($task);
	});
}

function getTaskId(){
	return new SystemCall(function(Task $task, Scheduler $scheduler){
		$task->setSendValue($task->getTaskId());
		$scheduler->schedule($task);
	});
}


function childTask(){
	$tid = (yield getTaskId());
	while(true){
		echo "Child task $tid still alive!\n";
		yield;
	}
}

function task(){
	$tid      = (yield getTaskId());
	$childTid = (yield addTask(childTask()));
	for($i = 1; $i <= 6; ++$i){
		echo "Parent task $tid iteration $i.\n";
		yield;
		if($i == 3){
			yield killTask($childTid);
		}
	}
}

function childTask1(){
	$tid = (yield getTaskId());
	while(true){
		echo "ChildTask $tid StillAlive!\n";
		yield;
	}
}

function task1(){
	$tid      = (yield getTaskId());
	$childTid = (yield addTask(childTask1()));
	for($i = 1; $i <= 6; ++$i){
		echo "ParentTask $tid Iteration $i.\n";
		yield;
		if($i == 3){
			yield killTask($childTid);
		}
	}
}

$scheduler = new Scheduler();
$scheduler->addTask(\sales\script\sales_info_sync\task());
$scheduler->addTask(task1());
$scheduler->run();