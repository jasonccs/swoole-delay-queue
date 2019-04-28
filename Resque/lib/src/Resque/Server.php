<?php

namespace Serve\Resque;

use Serve\Core\Extension;
use Serve\Core\Logger;
use Serve\Core\ProcessHelper;

abstract class Server
{
    private $serve = null;

    private $events = array(
        'Receive'        => 'onReceive',
        'ManagerStart' => 'onManagerStart',
        'Start' => 'onStart',
        'WorkerStart' => 'onWorkerStart',
        'Task' => 'onTask',
        'Finish' => 'onFinish',
    );

    protected function getServe(): ?\Swoole\Server
    {
        date_default_timezone_set('Asia/Shanghai');
        Extension::checkFailed();
        Logger::init();

        $this->serve = new \Swoole\Server(env('swoole.host'), env('swoole.port'));
        $this->serve->set([
            'worker_num'      => env('swoole.worker_num'),
            'daemonize'       => env('swoole.daemonize'),
            'log_file'        => env('swoole.log_dir'),
            'task_worker_num' => env('swoole.task_worker_num'),
            'max_request'     => env('swoole.max_request'),
            'task_max_request'=> env('swoole.task_max_request'),
        ]);

        foreach ($this->events as $name => $val)
        {
            $callback = array(
                $this,
                $val
            );
            $this->serve->on($name, $callback);
        }
        return $this->serve;
    }

    public function onReceive(\swoole_server $server, $fd, $reactorId, $data)
    {
        //todo:: 后期扩展 SWOOLE_CLIENT
    }

    /**
     * @return bool
     * Business Resque 进程是否已经运行
     */
    public static function isRunning(): bool
    {
        $pidFile = ProcessHelper::getMasterPidFile();
        if (file_exists($pidFile)) {
            $masterPid = @file_get_contents($pidFile);
            if (intval($masterPid) > 0 && is_numeric($masterPid)) {
                if (\Swoole\Process::kill($masterPid, 0)) {
                    return true;
                }
            }
        }
        return false;
    }

    public static function reloadAll(): bool
    {
        $masterPid = Server::getMasterPid();
        if ($masterPid > 0) {
            return ProcessHelper::killBySig($masterPid, SIGUSR1);
        }
        return false;
    }

    public static function reloadTaskWorker(): bool
    {
        $masterPid = Server::getMasterPid();
        if ($masterPid > 0) {
            return ProcessHelper::killBySig($masterPid, SIGUSR2);
        }
        return false;
    }

    public static function getMasterPid(): int
    {
        $masterPid = @file_get_contents(ProcessHelper::getMasterPidFile());
        if (intval($masterPid) > 0 && is_numeric($masterPid)) {
            if (\Swoole\Process::kill($masterPid, 0)) {
                return $masterPid;
            }
        }
        return 0;
    }

    public function onStart($server)
    {
        ProcessHelper::saveMasterPid($server->master_pid);
        $pidMaster = self::getMasterPid();
        Logger::notice("Resque started, Master pid is: {$pidMaster}.");
        ProcessHelper::setProcessName("Master: p{$pidMaster}");
    }

    public function onManagerStart($server)
    {
        ProcessHelper::setProcessName('Manager');
    }
}
