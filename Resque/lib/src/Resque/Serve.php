<?php

namespace Serve\Resque;

use app\job\Job;
use Serve\Core\ClientFactory;
use Serve\Core\Log;
use Serve\Core\Process;
use Serve\Exception\JobMissingException;
use Serve\Interfaces\IEvent;
use Serve\Interfaces\IJob;

/**
 * Class Serve
 * @package Serve\Resque
 * @version v1.0.1
 * @author twomiao
 */
class Serve extends Server implements IEvent
{
    private $job = null;

    public function onWorkerStart($server, $workerId)
    {
        if (function_exists('opcache_reset')) {
            \opcache_reset();
        }

        try {
            if (!class_exists(Job::class)) {
                throw new JobMissingException();
            }
            $this->job = new Job();

            if ($this->job instanceof IJob === false) {
                throw new JobMissingException([
                    'code' => -2,
                    'message' => 'The Job class is not defined.'
                ]);
            }
        } catch (JobMissingException $e) {
            Log::error($e->getMessage());
            Log::error("shutdown now server.");
            $server->shutdown();
        }

        if ($server->taskworker) {
            Process::daemonize("Task:{$workerId}");
            Log::info("Task:{$workerId} started.");
            $server->pdo = ClientFactory::makeClient('mysql');
        } else {
            Process::daemonize("Worker:{$workerId}");
            Log::info("Worker:{$workerId} started.");
            $server->redis = ClientFactory::makeClient('redis');

            try {
                // 每秒钟去队列查看是否有数据,如果存在数据发送给TASK 进程处理
                $server->tick(1000, function ($timerId) use ($server) {
                    $data = $this->job->getData($server->redis);
                    // 只要消息不为空,就投递
                    if (!empty($data) && is_string($data)) {
                        $server->task($data);
                    }
                    // todo:: 某种情况关闭计时器 $server->clearTimer($timerId);
                });
            } catch (\Exception $e) {
                Log::error($e->getMessage());
            }
        }
    }

    public function onTask($server, $taskId, $reactorId, $data)
    {
        $job = json_decode($data, true);
        if (!empty($job)) {
            $this->job->doJob($server->pdo, $job);
            $pid = posix_getpid();
            $data = "task:{$pid} has been completed. " . substr($data, 0, 45) . ' .....';
            $server->finish($data);
        } else {
            Log::debug($data);
        }
    }

    public function onFinish($server, $taskId, $data)
    {
        $this->job->finish($data);
    }

    /**
     * 启动Swoole
     */
    final public function run()
    {
        $this->getSwoole()->start();
    }
}