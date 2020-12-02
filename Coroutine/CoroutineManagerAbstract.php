<?php

namespace sales\coroutine;

use Generator;
use LFPhp\Logger\Logger;
use LFPhp\Logger\LoggerLevel;
use LFPhp\Logger\Output\ConsoleOutput;
use LFPhp\Logger\Output\FileOutput;
use function Lite\func\format_size;

/**
 * 协程管理器
 * Class CoroutineManager
 * @package sales
 * @method emergency (...$messages)
 * @method alert (...$messages)
 * @method critical (...$messages)
 * @method error (...$messages)
 * @method warning (...$messages)
 * @method notice (...$messages)
 * @method info (...$messages)
 * @method debug (...$messages)
 */
abstract class CoroutineManagerAbstract extends Scheduler {

	/** @var string 日志目录 */
	protected $log_dir = 'coroutine';

	/** @var Logger */
	protected $logger;

	/** @var int 最大协程数 */
	protected $max_coroutine_num;

	private $interval_microseconds = 1000;

	/**
	 * 获取日志目录和文件名
	 * @return array ['目录','文件名']
	 */
	protected function getLogFileInfo(){
		$path_list    = explode(DIRECTORY_SEPARATOR, str_replace('\\', DIRECTORY_SEPARATOR, static::class ?? pathinfo(__FILE__, PATHINFO_FILENAME)));
		$log_filename = end($path_list);
		$log_dir      = sys_get_temp_dir().DIRECTORY_SEPARATOR.$this->log_dir.DIRECTORY_SEPARATOR;
		return [$log_dir, $log_filename];
	}

	/**
	 * 设置进程脚本记录器
	 * registerWhile 会操作静态变量 导致内存泄漏
	 * 根据具体调用的类名作为记录 log 的文件名
	 */
	protected function setScriptLogger(){
		[$log_dir, $log_filename] = $this->getLogFileInfo();
		$log_dir_prefix = $log_dir.$log_filename;
		$this->logger   = Logger::instance($log_filename);
		$this->logger->register(new ConsoleOutput(), LoggerLevel::DEBUG);
		$this->logger->register(new FileOutput($log_dir_prefix.'.error.log'), LoggerLevel::ERROR);
	}

	/**
	 * 输出内存使用情况(返回当前分配给你的 PHP 脚本的内存量，单位是字节（byte）)
	 * @param bool  $real_usage 如果设置为 TRUE，获取系统分配总的内存尺寸，包括未使用的页。如果未设置或者设置为 FALSE，仅仅报告实际使用的内存量
	 * @param array $messages   追加输出信息
	 */
	final protected function outputMemoryUsage($real_usage = false, ...$messages){
		$this->debug(Logger::combineMessages($messages), 'used internal memory:', format_size(memory_get_usage($real_usage)), 'Peak allocated memory:', format_size(memory_get_peak_usage($real_usage)));
	}

	/**
	 * 检查时间(声明当前时间允许执行)
	 * @return bool
	 */
	protected function assertAllowExecution(){
		return true;
	}

	/**
	 * call log method
	 * @param $level_method
	 * @param $messages
	 * @return mixed
	 * @method emergency (...$messages)
	 * @method alert (...$messages)
	 * @method critical (...$messages)
	 * @method error (...$messages)
	 * @method warning (...$messages)
	 * @method notice (...$messages)
	 * @method info (...$messages)
	 * @method debug (...$messages)
	 */
	final public function __call($level_method, $messages){
		return $this->logger->{$level_method}(Logger::combineMessages($messages));
	}

	public function __construct($max_coroutine_count = 10){
		$this->max_coroutine_num = (int)$max_coroutine_count > 0 ? $max_coroutine_count : 10;
		$this->setScriptLogger();
		parent::__construct();
	}

	/**
	 * 添加协程任务
	 * @param \Generator $coroutine
	 * @return \sales\coroutine\SystemCall
	 */
	public function addCoroutineTask(Generator $coroutine){
		return new SystemCall(function(Task $task, Scheduler $scheduler) use ($coroutine){
			$task->setSendValue($scheduler->addTask($coroutine));
			$scheduler->schedule($task);
		});
	}

	/**
	 * 获取协程任务ID
	 * @return \sales\coroutine\SystemCall
	 */
	public function getCoroutineTaskId(){
		return new SystemCall(function(Task $task, Scheduler $scheduler){
			$task->setSendValue($task->getTaskId());
			$scheduler->schedule($task);
		});
	}

	/**
	 * 杀死协程任务
	 * @param $tid
	 * @return \sales\coroutine\SystemCall
	 */
	public function killCoroutineTask($tid){
		return new SystemCall(function(Task $task, Scheduler $scheduler) use ($tid){
			$task->setSendValue($scheduler->killTask($tid));
			$scheduler->schedule($task);
		});
	}

	/**
	 * 繁忙检测
	 * @return bool
	 */
	public function isBusy(){
		return count($this->taskMap) >= $this->max_coroutine_num;
	}

	/**
	 * 循环检测
	 */
	private function _loopSlaveProcess(){
		while($this->isBusy()){
			sleep(3);
			usleep($this->interval_microseconds);
		}
	}

	/**
	 * 添加任务
	 * 达到最大协程数就执行协程 否则 循环检测协程
	 * @param \Generator $coroutine
	 * @return int|void
	 */
	public function addTask($coroutine){
		$tid = parent::addTask($coroutine);
		$this->isBusy() ? $this->run() : $this->_loopSlaveProcess();
		return $tid;
	}

	/**
	 * 开始执行主流程
	 * @param array $params
	 */
	public function handleCoroutine($params = []){
		$this->coroutineHandler($params);
	}

	/**
	 * 协程
	 * @param $params
	 * @return mixed
	 */
	abstract public function coroutine($params);

	/**
	 * 协程主流程
	 * @param $params
	 * @return mixed
	 */
	abstract public function coroutineHandler($params);

}