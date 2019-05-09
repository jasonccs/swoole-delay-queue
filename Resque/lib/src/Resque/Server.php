<?php

namespace Serve\Resque;

use Serve\Core\Environment;
use Serve\Core\Log;
use Serve\Core\Process;

/**
 * Class Server
 * @package Serve\Resque
 * @version v1.0.1
 * @author twomiao
 */
abstract class Server
{
    /**
     * @var null
     * swoole handle
     */
    private $_serve = null;

    /**
     * @var array
     * swoole 事件
     */
    protected $events = array(
        'Receive' => 'onReceive',
        'ManagerStart' => 'onManagerStart',
        'Start' => 'onStart',
        'WorkerStart' => 'onWorkerStart',
        'Task' => 'onTask',
        'Finish' => 'onFinish',
    );

    protected function getSwoole(): ?\Swoole\Server
    {
        date_default_timezone_set('Asia/Shanghai');

        Environment::checkOrFailed();

        $this->_serve = new \Swoole\Server(env('swoole.host'), env('swoole.port'));

        $initLog = Log::getLogDir() . 'error' . DS;
        if (!is_dir($initLog)) {
            @mkdir($initLog, 0777, true);
        }
        $initLog .=  date('Ymd') . '.log';

        $this->_serve->set([
            'worker_num' => env('swoole.worker_num'),
            'daemonize' => env('swoole.daemonize'),
            'log_file' => $initLog,
            'task_worker_num' => env('swoole.task_worker_num'),
            'max_request' => env('swoole.max_request'),
            'task_max_request' => env('swoole.task_max_request'),
        ]);

        foreach ($this->events as $name => $val) {
            $callback = array(
                $this,
                $val
            );
            $this->_serve->on($name, $callback);
        }
        return $this->_serve;
    }

    public function onReceive(\swoole_server $server, $fd, $reactorId, $data)
    {
        //todo:: 后期扩展 SWOOLE_CLIENT
    }

    public function onStart($server)
    {
        Process::setMasterPid($server->master_pid);
        $masterPid = Process::getMasterPid();
        Log::info(" Serve started, Master pid is: {$masterPid}.");
        Process::daemonize("Master: p{$masterPid}");
    }

    public function onManagerStart($server)
    {
        Process::daemonize('Manager');
    }
}
