<?php

namespace Serve\Resque;

use app\logic\Job;
use Serve\Colors\Color;
use Serve\Colors\ColorText;
use Serve\Core\ClientFactory;
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
            Color::println("[ ERROR ] => " . $e->getMessage(), ColorText::RED_FONT);
            $server->shutdown();
        }

        $this->job = new Job();
        if ($server->taskworker) {
            ProcessHelper::setProcessName("TaskWorker:{$workerId}");
            Color::print("[ OK ]", ColorText::RED_FONT);
            Color::println("tTaskWorker:{$workerId} started.");
            $server->pdo = ClientFactory::makeClient('mysql');
        } else {
            ProcessHelper::setProcessName("Worker:{$workerId}");
            Color::print("[ OK ]", ColorText::YELLOW_FONT);
            Color::println("tWorker:{$workerId} started.");
            $server->redis = ClientFactory::makeClient('predis');
            Timer::interval(function ($timerId) use ($server, $workerId) {
                $data = $this->job->dequeue($server->redis);
                // 只要消息不为空,就投递
                if (!empty($data)) {
                    $server->task($data);
                }
            }, 1000);
        }
    }

    public function onTask($server, $taskId, $reactorId, $data)
    {
        $this->job->business($server->pdo, json_decode($data, true));
        $data = substr($data, 0, 45) . ' .....';
        $server->finish("Queue message processing successful: {$data}");
    }

    public function onFinish($server, $taskId, $data)
    {
        Color::print("[ task:{$taskId} ]", ColorText::YELLOW_FONT);
        Color::println($data, ColorText::GREEN_FONT);
    }

    final public static function run()
    {
        (new self)->getServe()->start();
    }
}