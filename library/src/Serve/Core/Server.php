<?php

namespace Serve\Core;

abstract class Server
{
    private $serve = null;

    protected function getSwooleServer(): ?\Swoole\Server
    {
        date_default_timezone_set('Asia/Shanghai');
        Extension::checkInstalled();
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

        $callbacks = array(
            'Receive'        => [$this, 'onReceive'],
            'ManagerStart'   => [$this, 'onManagerStart'],
            'Start'          => [$this, 'onStart'],
            'WorkerStart'    => [$this, 'onWorkerStart'],
            'Task'           => [$this, 'onTask'],
            'Finish'         => [$this, 'onFinish'],
        );

        foreach ($callbacks as $event => $callback) {
            $this->serve->on($event, $callback);
        }
        return $this->serve;
    }

    /**
     * @return bool
     * Business Server 进程是否已经运行
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

    public function onStart($serve)
    {
        ProcessHelper::saveMasterPid($serve->master_pid);
        $pidMaster = self::getMasterPid();
        Logger::notice("Business server started, Master pid is: {$pidMaster}.");
        ProcessHelper::setProcessName("Master: p{$pidMaster}");
    }

    public function onManagerStart($serve)
    {
        ProcessHelper::setProcessName('Manager');
    }
}
