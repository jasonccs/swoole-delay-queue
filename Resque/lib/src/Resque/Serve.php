<?php

namespace Serve\Resque;

use app\logic\Job;
use Serve\Colors\Color;
use Serve\Colors\ColorText;
use Serve\Core\ClientFactory;
use Serve\Core\Log;
use Serve\Core\ProcessHelper;
use Serve\Core\Timer;
use Serve\Exception\JobClassNotFoundException;
use Serve\Interfaces\EventInterface;

class Serve extends Server implements EventInterface
{
    private $job = null;

    public function onWorkerStart($server, $workerId)
    {
        if (function_exists('opcache_reset')) {
            \opcache_reset();
        }
        try {
            if (!class_exists(Job::class)) {
                throw new JobClassNotFoundException();
            }
        } catch (JobClassNotFoundException $e) {
            Log::error($e->getMessage());
            Log::error("shutdown now server.");
            $server->shutdown();
        }

        $this->job = new Job();
        if ($server->taskworker) {
            ProcessHelper::setProcessName("TaskWorker:{$workerId}");
            Log::info("TaskWorker: {$workerId} started.");
            $server->pdo = ClientFactory::makeClient('mysql');
        } else {
            ProcessHelper::setProcessName("Worker:{$workerId}");
            Log::info("Worker: {$workerId} started.");
            $server->redis = ClientFactory::makeClient('predis');

            $server->tick(1000, function($timerId) use($server) {
                $data = $this->job->dequeue($server->redis);
                // 只要消息不为空,就投递
                if (!empty($data)) {
                    $server->task($data);
                }
                // todo:: 某种情况关闭计时器 $server->clearTimer($timerId);
            });
        }
    }

    public function onTask($server, $taskId, $reactorId, $data)
    {
        $this->job->business($server->pdo, $data);
        $pid = posix_getpid();
        $data = "task:{$pid} has been completed. " .substr($data, 0, 45) . ' .....';
        $server->finish($data);
    }

    public function onFinish($server, $taskId, $data)
    {
        Log::debug($data);
    }

    final public static function run()
    {
        (new self)->getServe()->start();
    }
}