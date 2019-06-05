<?php

/**
 * Class Task
 */
class Task
{

    protected $taskId;

    protected $coroutine;

    protected $sendValue        = NULL;

    protected $beforeFirstYield = TRUE;

    protected $exception        = NULL;

    public function __construct($taskId, Generator $coroutine)
    {

        $this->taskId    = $taskId;
        $this->coroutine = $coroutine;
    }

    public function getTaskId()
    {

        return $this->taskId;
    }

    public function setSendValue($sendValue)
    {

        $this->sendValue = $sendValue;
    }

    public function setException($exception)
    {

        $this->exception = $exception;
    }

    public function run()
    {

        if ($this->beforeFirstYield) {
            $this->beforeFirstYield = FALSE;

            return $this->coroutine->current();
        } else if ($this->exception) {
            $retval          = $this->coroutine->throw($this->exception);
            $this->exception = NULL;

            return $retval;
        } else {
            $retval          = $this->coroutine->send($this->sendValue);
            $this->sendValue = NULL;

            return $retval;
        }
    }

    public function isFinished()
    {

        return !$this->coroutine->valid();
    }
}

/**
 * @Desc 调度器  @Editor: wili-lixiang <wili.lixiang@gmail.com> 2019/5/22 16:29:56
 */
class Scheduler
{

    protected $maxTaskId       = 0;

    protected $taskMap         = []; // taskId => task

    protected $taskQueue;

    protected $waitingForRead  = [];

    protected $waitingForWrite = [];

    public function __construct()
    {

        $this->taskQueue = new SplQueue();
    }

    public function newTask(Generator $coroutine)
    {

        $taskId = ++$this->maxTaskId;
        try {
            $task = new Task($taskId, $coroutine);
        } catch (\Exception $e) {
            printf($e->getMessage());
        }
        $this->taskMap[$taskId] = $task;
        $this->schedule($task);

        return $taskId;
    }

    public function killTask($tid)
    {

        if (!isset($this->taskMap[$tid])) {
            return FALSE;
        }

        unset($this->taskMap[$tid]);

        // This is a bit ugly and could be optimized so it does not have to walk the queue,
        // but assuming that killing tasks is rather rare I won't bother with it now
        foreach ($this->taskQueue as $i => $task) {
            if ($task->getTaskId() === $tid) {
                unset($this->taskQueue[$i]);
                break;
            }
        }

        return TRUE;
    }

    public function schedule(Task $task)
    {

        $this->taskQueue->enqueue($task);
    }

    public function run()
    {

        while (!$this->taskQueue->isEmpty()) {
            $task   = $this->taskQueue->dequeue();
            $retval = $task->run();

            if ($retval instanceof SystemCall) {
                try {
                    $retval($task, $this);
                } catch (Exception $e) {
                    $task->setException($e);
                    $this->schedule($task);
                }
                continue;
            }
            if ($task->isFinished()) {
                unset($this->taskMap[$task->getTaskId()]);
            } else {
                $this->schedule($task);
            }
        }
    }

    public function waitForRead($socket, Task $task)
    {

        if (isset($this->waitingForRead[(int)$socket])) {
            $this->waitingForRead[(int)$socket][1][] = $task;
        } else {
            $this->waitingForRead[(int)$socket] = [$socket, [$task]];
        }
    }

    public function waitForWrite($socket, Task $task)
    {

        if (isset($this->waitingForWrite[(int)$socket])) {
            $this->waitingForWrite[(int)$socket][1][] = $task;
        } else {
            $this->waitingForWrite[(int)$socket] = [$socket, [$task]];
        }
    }

    protected function ioPoll($timeout)
    {

        $rSocks = [];
        foreach ($this->waitingForRead as list($socket)) {
            $rSocks[] = $socket;
        }

        $wSocks = [];
        foreach ($this->waitingForWrite as list($socket)) {
            $wSocks[] = $socket;
        }

        $eSocks = []; // dummy

        if (!stream_select($rSocks, $wSocks, $eSocks, $timeout)) {
            return;
        }

        foreach ($rSocks as $socket) {
            list(, $tasks) = $this->waitingForRead[(int)$socket];
            unset($this->waitingForRead[(int)$socket]);

            foreach ($tasks as $task) {
                $this->schedule($task);
            }
        }

        foreach ($wSocks as $socket) {
            list(, $tasks) = $this->waitingForWrite[(int)$socket];
            unset($this->waitingForWrite[(int)$socket]);

            foreach ($tasks as $task) {
                $this->schedule($task);
            }
        }
    }

    protected function ioPollTask()
    {

        while (TRUE) {
            if ($this->taskQueue->isEmpty()) {
                $this->ioPoll(NULL);
            } else {
                $this->ioPoll(0);
            }
            yield;
        }
    }
}

/**
 * Class SystemCall 系统调用
 */
class SystemCall
{

    protected $callback;

    public function __construct(callable $callback)
    {

        $this->callback = $callback;
    }

    public function __invoke(Task $task, Scheduler $scheduler)
    {

        $callback = $this->callback;

        return $callback($task, $scheduler);
    }
}

/**
 * 协程堆栈
 *
 * @var mixed
 */
class CoroutineReturnValue
{

    protected $value;

    public function __construct($value)
    {

        $this->value = $value;
    }

    public function getValue()
    {

        return $this->value;
    }
}

/**
 *
 * @var mixed
 */
class CoSocket
{

    protected $socket;

    public function __construct($socket)
    {

        $this->socket = $socket;
    }

    public function accept()
    {

        yield waitForRead($this->socket);
        yield retval(new CoSocket(stream_socket_accept($this->socket, 0)));
    }

    public function read($size)
    {

        yield waitForRead($this->socket);
        yield retval(fread($this->socket, $size));
    }

    public function write($string)
    {

        yield waitForWrite($this->socket);
        fwrite($this->socket, $string);
    }

    public function close()
    {

        @fclose($this->socket);
    }
}

function task1()
{

    for ($i = 1; $i <= 10; ++$i) {
        echo "This is task 1 iteration $i.\n";
        yield;
    }
}

function task2()
{

    for ($i = 1; $i <= 5; ++$i) {
        echo "This is task 2 iteration $i.\n";
        yield;
    }
}

function getTaskId()
{

    return new SystemCall(function (Task $task, Scheduler $scheduler) {

        $task->setSendValue($task->getTaskId());
        $scheduler->schedule($task);
    });
}

function newTask(Generator $coroutine)
{

    return new SystemCall(
        function (Task $task, Scheduler $scheduler) use ($coroutine) {

            $task->setSendValue($scheduler->newTask($coroutine));
            $scheduler->schedule($task);
        }
    );
}

function killTask($tid)
{

    return new SystemCall(
        function (Task $task, Scheduler $scheduler) use ($tid) {

            $task->setSendValue($scheduler->killTask($tid));
            $scheduler->schedule($task);
        }
    );
}

function task($max)
{

    $tid = (yield getTaskId()); // <-- here's the syscall!
    for ($i = 1; $i <= $max; ++$i) {
        echo "This is task $tid iteration $i.\n";
        yield;
    }
}

function childTask()
{

    $tid = (yield getTaskId());
    while (TRUE) {
        echo "Child task $tid still alive!\n";
        yield;
    }
}

function task0()
{

    $tid      = (yield getTaskId());
    $childTid = (yield newTask(childTask()));

    for ($i = 1; $i <= 6; ++$i) {
        echo "Parent task $tid iteration $i.\n";
        yield;

        if ($i == 3) yield killTask($childTid);
    }
}

function waitForRead($socket)
{

    return new SystemCall(
        function (Task $task, Scheduler $scheduler) use ($socket) {

            $scheduler->waitForRead($socket, $task);
        }
    );
}

function waitForWrite($socket)
{

    return new SystemCall(
        function (Task $task, Scheduler $scheduler) use ($socket) {

            $scheduler->waitForWrite($socket, $task);
        }
    );
}

function server($port)
{

    echo "Starting server at port $port...\n";

    $socket = @stream_socket_server("tcp://localhost:$port", $errNo, $errStr);
    if (!$socket) throw new Exception($errStr, $errNo);

    stream_set_blocking($socket, 0);

    while (TRUE) {
        yield waitForRead($socket);
        $clientSocket = stream_socket_accept($socket, 0);
        yield newTask(handleClient($clientSocket));
    }
}

function handleClient($socket)
{

    yield waitForRead($socket);
    $data = fread($socket, 8192);

    $msg       = "Received following request:\n\n$data";
    $msgLength = strlen($msg);
    $response  = <<<RES
HTTP/1.1 200 OK\r
Content-Type: text/plain\r
Content-Length: $msgLength\r
Connection: close\r
\r
$msg\r
RES;
    yield waitForWrite($socket);
    fwrite($socket, $response);

    fclose($socket);
}

function echoTimes($msg, $max)
{

    for ($i = 1; $i <= $max; ++$i) {
        echo "$msg iteration $i\n";
        yield;
    }
}

function task3()
{

    echoTimes('foo', 10); // print foo ten times
    echo "---\n";
    echoTimes('bar', 5); // print bar five times
    yield; // force it to be a coroutine
}

function retval($value)
{

    return new CoroutineReturnValue($value);
}

function stackedCoroutine0(Generator $gen)
{

    $stack = new SplStack;

    for (; ;) {
        $value = $gen->current();

        if ($value instanceof Generator) {
            $stack->push($gen);
            $gen = $value;
            continue;
        }

        $isReturnValue = $value instanceof CoroutineReturnValue;
        if (!$gen->valid() || $isReturnValue) {
            if ($stack->isEmpty()) {
                return;
            }

            $gen = $stack->pop();
            $gen->send($isReturnValue ? $value->getValue() : NULL);
            continue;
        }

        $gen->send(yield $gen->key() => $value);
    }
}

function stackedCoroutine(Generator $gen)
{

    $stack     = new SplStack;
    $exception = NULL;

    for (; ;) {
        try {
            if ($exception) {
                $gen->throw($exception);
                $exception = NULL;
                continue;
            }

            $value = $gen->current();

            if ($value instanceof Generator) {
                $stack->push($gen);
                $gen = $value;
                continue;
            }

            $isReturnValue = $value instanceof CoroutineReturnValue;
            if (!$gen->valid() || $isReturnValue) {
                if ($stack->isEmpty()) {
                    return;
                }

                $gen = $stack->pop();
                $gen->send($isReturnValue ? $value->getValue() : NULL);
                continue;
            }

            try {
                $sendValue = (yield $gen->key() => $value);
            } catch (Exception $e) {
                $gen->throw($e);
                continue;
            }

            $gen->send($sendValue);
        } catch (Exception $e) {
            if ($stack->isEmpty()) {
                throw $e;
            }

            $gen       = $stack->pop();
            $exception = $e;
        }
    }
}

function server1($port)
{

    echo "Starting server at port $port...\n";

    $socket = @stream_socket_server("tcp://localhost:$port", $errNo, $errStr);
    if (!$socket) throw new Exception($errStr, $errNo);

    stream_set_blocking($socket, 0);

    $socket = new CoSocket($socket);
    while (TRUE) {
        yield newTask(
            handleClient(yield $socket->accept())
        );
    }
}

function handleClient1($socket)
{

    $data = (yield $socket->read(8192));

    $msg       = "Received following request:\n\n$data";
    $msgLength = strlen($msg);

    $response = <<<RES
HTTP/1.1 200 OK\r
Content-Type: text/plain\r
Content-Length: $msgLength\r
Connection: close\r
\r
$msg
RES;

    yield $socket->write($response);
    yield $socket->close();
}

function gen()
{

    echo "Foo\n";
    try {
        yield;
    } catch (Exception $e) {
        echo "Exception: {$e->getMessage()}\n";
    }
    echo "Bar\n";
}

function killTask1($tid)
{

    return new SystemCall(
        function (Task $task, Scheduler $scheduler) use ($tid) {

            if ($scheduler->killTask($tid)) {
                $scheduler->schedule($task);
            } else {
                throw new InvalidArgumentException('Invalid task ID!');
            }
        }
    );
}

function task4()
{

    try {
        yield killTask1(500);
    } catch (Exception $e) {
        echo 'Tried to kill task 500 but failed: ', $e->getMessage(), "\n";
    }
}

try {

    /*$gen = gen();
    $gen->rewind();                    
    $gen->throw(new Exception('Test'));*/
    /*$scheduler = new Scheduler;
    $scheduler->newTask(task3());
    $scheduler->run();*/
    /*$scheduler = new Scheduler;
    $scheduler->newTask(server(8000));
    $scheduler->run();*/
    /*$scheduler = new Scheduler();
    $scheduler->newTask(task1());
    $scheduler->newTask(task2());

    $scheduler->run();*/

    /*$scheduler = new Scheduler;

    $scheduler->newTask(task0(10));
    $scheduler->newTask(task0(5));

    $scheduler->run();*/
} catch (\Exception $e) {
    printf($e->getMessage());
}