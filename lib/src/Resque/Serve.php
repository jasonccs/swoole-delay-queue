<?php

namespace Serve\Resque;

use app\Job\Job;
use Serve\Colors\Color;
use Serve\Colors\ColorText;
use Serve\Core\EnvCheck;
use Serve\Core\Helper;
use Serve\Core\Log;
use Serve\Core\MysqlClient;
use Serve\Core\RedisClient;
use Serve\Exception\JobException;
use Serve\Interfaces\IEvent;
use Serve\Interfaces\IJob;
use Swoole\Server;

/**
 * Class Serve
 * @package Serve\Resque
 * @author twomiao:<995200452@qq.com>
 */
class Serve implements IEvent
{
    /**
     * @var null
     * job
     */
    protected $job = null;

    /**
     * @var null
     * Swoole 句柄
     */
    protected $serve = null;

    /**
     * @var string
     * 守护进程名
     */
    protected $name = 'Serve Queue';

    /**
     * @var bool
     * 守护进程运行
     */
    protected $daemon = false;

    /**
     * @var array
     * Swoole 运行事件
     */
    protected $events = array(
        'Receive' => 'onReceive',
        'ManagerStart' => 'onManagerStart',
        'Start' => 'onStart',
        'WorkerStart' => 'onWorkerStart',
        'Task' => 'onTask',
        'Finish' => 'onFinish',
    );

    public function __construct(string $daemon)
    {
        if ($daemon == '-d')
        {
            $this->daemon = true;
        }
    }

    public function onWorkerStart($serve, $workerId)
    {
        if (function_exists('opcache_reset')) {
            \opcache_reset();
        }

        try {
            if (!class_exists(Job::class)) {
                throw new JobException([
                    'code' => -1,
                    'message' => 'The Job class is not defined.'
                ]);
            }

            $this->job = new Job();

            if ($this->job instanceof IJob === false) {
                throw new JobException([
                    'code' => -2,
                    'message' => 'The IJob interface was not found.'
                ]);
            }

            if ($serve->taskworker) {
                Helper::pname("(task:{$workerId})");
                Log::debug("task({$workerId}) started.");
                $serve->pdo = MysqlClient::makeInstance();
            } else {
                Helper::pname("(worker:{$workerId})");
                Log::debug("worker({$workerId}) started.");
                $serve->redis = RedisClient::makeInstance();

                // 每秒钟去队列查看是否有数据,如果存在数据发送给TASK 进程处理
                $serve->tick(env('swoole.crontab'), function ($timerId) use ($serve) {
                    $data = $this->job->getData($serve->redis);
                    // 只要消息不为空,就投递
                    if ($this->valueString($data) !== false) {
                        $serve->task($data);
                    }
                    // todo:: 某种情况关闭计时器 $server->clearTimer($timerId);
                });
            }
        } catch (JobException $e) {
            Log::error($e->getMessage());
            $serve->shutdown();
        } catch(\Throwable $throwable) {
            Log::error($throwable->getMessage());
            $serve->shutdown();
        }
    }

    public function onTask($server, $taskId, $reactorId, $data)
    {
        $job = json_decode($data, true);
        if (!empty($job)) {
            // 返回不是空的并且是字符串就发送给finish 回调函数处理任务
            $done = $this->job->doJob($job);
            if ($this->valueString($done) !== false) {
                $server->finish($done);
            }
        } else {
            Log::error($data);
        }
    }

    public function onFinish($server, $taskId, $data)
    {
        $this->job->finish($data);
    }

    /**
     * Started Swoole
     */
    final public function run() : void
    {
        // 检测环境是否通过
        EnvCheck::pass();

        $this->serve = new Server(env('swoole.host'), env('swoole.port'));
        $this->serve->set([
            'worker_num' => env('swoole.worker_num'),
            'daemonize' => $this->daemon,
            'log_file' => Log::create(),
            'task_worker_num' => env('swoole.task_worker_num'),
            'max_request' => 5000,
            'task_max_request' => env('swoole.task_max_request'),
        ]);

        foreach ($this->events as $name => $val) {
            $callback = array(
                $this,
                $val
            );
            $this->serve->on($name, $callback);
        }

        $this->serve->start();
    }

    /**
     * @param $data
     * @return bool
     * 非空并且为字符串
     */
    private function valueString($data): bool
    {
        if (!empty($data) && is_string($data)) {
            return true;
        }
        return false;
    }

    public function onReceive($serve) {

    }

    public function onManagerStart($serve)
    {
        Helper::pname('Serve::Manager');
    }

    public function onStart($serve) {
        Helper::setMasterPid($serve->master_pid);
        $masterPid = Helper::getMasterPid();
        Log::info("Service already running, main process number:{$masterPid}");
        Helper::pname("Serve::master");
    }
}