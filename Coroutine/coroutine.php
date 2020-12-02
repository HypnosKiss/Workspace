<?php

namespace sales\script\sales_info_sync;

use Generator;
use Lite\Core\Application;
use Lite\DB\Driver\DBAbstract;
use sales\coroutine\CoroutineManagerAbstract;

require_once 'vendor/autoload.php';
require_once 'server_config/ServerConfig.php';
require_once 'lib/autoload.php';

$levels    = 4;
$sale_root = dirname(__DIR__, $levels).'/sale/';
Application::init('sales', dirname(__DIR__, $levels).'/erp/', Application::MODE_CLI);
Application::addIncludePath($sale_root.'app/', 'sales\sale');
Application::addIncludePath($sale_root.'app/include/', 'sales\sale');
DBAbstract::distinctQueryOff();

class testCoroutine extends CoroutineManagerAbstract {

	public function coroutine($max){
		$tid = yield $this->getCoroutineTaskId(); // <-- here's the syscall!
		for($i = 1; $i <= $max; ++$i){
			$this->debug("This is task $tid iteration $i.");
			yield;
		}
		yield;
	}

	public function coroutineHandler($params = []){
		$task = rand(1, 9);
		while($task){
			$tid = $this->addTask($this->coroutine($task));
			$this->outputMemoryUsage(false, "Parent task $tid .");
		}
	}

	/**
	 * 任务处理程序
	 * @param $params
	 * @return Generator
	 */
	public function taskHandler($params){
		// TODO: Implement taskHandler() method.
		$this->debug('taskHandler', $params);
		yield;
	}
}

$scheduler = new testCoroutine();
$scheduler->handleCoroutine();