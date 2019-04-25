<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/31 0031
 * Time: 下午 12:14
 */

namespace Serve\Core;

use app\logic\Job;
use Serve\Colors\Color;
use Serve\Colors\ColorText;
use Serve\Exception\JobClassNotFoundException;

class Serve extends Server
{
    private $job = null;

    public function onWorkerStart(\swoole_server $serve, $workerId)
    {
        if (function_exists('opcache_reset')) {
            \opcache_reset();
        }

        require_once dirname(__DIR__) . '/../'.PathManager::JOB_PATH;

        try {
            if (!class_exists('app\logic\Job')) {
                throw new JobClassNotFoundException();
            }
        } catch (JobClassNotFoundException $e) {
            print_r($e->getMessage());
            $serve->shutdown();
        }

        $this->job = new Job();
        if ($serve->taskworker) {
            ProcessHelper::setProcessName("TaskWorker:{$workerId}");
            Color::print("[ OK ]", ColorText::RED_FONT);
            Color::println( "tTaskWorker:{$workerId} started.");
            $serve->pdo = ClientFactory::makeClient('mysql');
        } else {
            ProcessHelper::setProcessName("Worker:{$workerId}");
            Color::print("[ OK ]", ColorText::YELLOW_FONT);
            Color::println("tWorker:{$workerId} started.");
            $serve->redis = ClientFactory::makeClient('predis');
            Timer::interval(function ($timerId) use ($serve, $workerId) {
                $data = $this->job->dequeue($serve->redis);
                // 只要消息不为空,就投递
                if (!empty($data)) {
                    $serve->task($data);
                }
            }, 1000);
        }
    }

    public function onReceive(\swoole_server $serve, $fd, $reactorId, $data)
    {
        //todo::
    }

    public function onTask($serve, $taskId, $reactorId, $data)
    {
        $this->job->business($serve->pdo, json_decode($data, true));
        $data = substr($data, 0, 45) . ' .....';
        $serve->finish("Queue message processing successful: {$data}");
    }

    public function onFinish($serve, $taskId, $data)
    {
        Color::print("[ task:{$taskId} ]", ColorText::YELLOW_FONT);
        Color::println($data, ColorText::GREEN_FONT);
    }

    final public static function run()
    {
        (new self)->getSwooleServer()->start();
    }
}