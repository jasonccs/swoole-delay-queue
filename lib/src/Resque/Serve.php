<?php

namespace Serve\Resque;

use app\Job\Job;
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
    protected $serveHandler = null;

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

    private $tube = 'delayer::order_queue';

    public function __construct(string $daemon)
    {
        if ($daemon == '-d') {
            $this->daemon = true;
        }
    }

    public function onWorkerStart($serveHandler, $workerId)
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

            if ($serveHandler->taskworker) {
                Helper::pname("task #{$workerId}");
                Log::debug("task({$workerId}) started.");
                $serveHandler->pdo = MysqlClient::makeInstance();
            } else {
                Helper::pname("worker #{$workerId}");
                Log::debug("worker({$workerId}) started.");
                $serveHandler->redis = RedisClient::makeInstance();

                // 每秒钟去队列查看是否有数据,如果存在数据发送给TASK 进程处理
                $crontab = intval(env('swoole.crontab')) ?: 1000;
                $serveHandler->tick($crontab, function ($timerId)
                use ($serveHandler) {
                    $data = $this->getDataByQueue($serveHandler);
                    // 只要消息不为空,就投递
                    if ($this->mustBeString($data)) {
                        $serveHandler->task($data);
                    }
                    // todo:: 某种情况关闭计时器 $server->clearTimer($timerId);
                });
            }
        } catch (JobException $e) {
            Log::error($e->getMessage());
            $serveHandler->shutdown();
        } catch (\Error $error) {
            Log::error($error->getMessage());
            $serveHandler->shutdown();
        } catch (\Throwable $throwable) {
            Log::error($throwable->getMessage());
            $serveHandler->shutdown();
        }
    }

    public function onTask($serveHandler, $taskId, $reactorId, $data)
    {
        if ($this->mustBeString($data)) {
            $data = json_decode($data, true);
            if (!empty($data)) {
                // 返回不是空的并且是字符串就发送给finish 回调函数处理任务
                $done = $this->job->doJob($data, $serveHandler->pdo);
                if ($this->mustBeString($done) !== false) {
                    $serveHandler->finish($done);
                }
            }
        }
    }

    public function onFinish($serveHandler, $taskId, $data)
    {
        $this->job->after($data);
    }

    /**
     * Started Swoole
     */
    final public function run(): void
    {
        // 检测环境是否通过
        EnvCheck::pass();

        $this->serveHandler = new Server(env('swoole.host'), env('swoole.port'));
        $this->serveHandler->set([
            'worker_num' => env('swoole.worker_num'),
            'daemonize' => $this->daemon,
            'log_file' => Log::create(),
            'task_worker_num' => env('swoole.task_worker_num'),
            'max_request' => 5000,
            'task_max_request' => env('swoole.task_max_request'),
        ]);

        foreach ($this->events as $name => $event) {
            $callback = array(
                $this,
                $event
            );
            $this->serveHandler->on($name, $callback);
        }

        $this->serveHandler->start();
    }

    private function getDataByQueue($serveHandler): ?string
    {
        $message = $serveHandler->redis->bPop($this->tube, 2);
        if ($message !== false) {
            return $message->body;
        }
        return null;
    }

    /**
     * @param string $data 必须为有效字符串
     * @return bool
     */
    private function mustBeString(?string $data): bool
    {
        if (empty($data)) {
            return false;
        }

        if (!hash_equals($data, '') && strlen($data) > 0) {
            return true;
        }
        return false;
    }

    public function onReceive($serve)
    {

    }

    public function onManagerStart($serveHandler)
    {
        Helper::pname('manager');
    }

    public function onStart($serveHandler)
    {
        Helper::setMasterPid($serveHandler->master_pid);
        $masterPid = Helper::getMasterPid();
        Log::debug("Serve-Queue already running, Master PID:{$masterPid}");
        Helper::pname("master");
    }
}
