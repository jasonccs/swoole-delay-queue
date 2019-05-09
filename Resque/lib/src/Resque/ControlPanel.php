<?php

namespace Serve\Resque;

use Serve\Core\Process;

/**
 * Class ControlPanel
 * @package Serve\Resque
 * @author twomiao
 */
class ControlPanel
{
    /**
     * @return bool
     * 服务是否已运行
     */
    public static function isRunning(): bool
    {
        $masterPid = Process::getMasterPid();
        if (intval($masterPid) > 0 && is_numeric($masterPid)) {
            if (Process::kill($masterPid, 0)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return bool
     * reload task and worker
     */
    public static function reloadAll(): bool
    {
        $masterPid = Process::getMasterPid();
        if ($masterPid > 0) {
            return Process::killBySig($masterPid, SIGUSR1);
        }
        return false;
    }

    /**
     * @return bool
     * reload task
     */
    public static function reloadTask(): bool
    {
        $masterPid = Process::getMasterPid();
        if ($masterPid > 0) {
            return Process::killBySig($masterPid, SIGUSR2);
        }
        return false;
    }

    /**
     * @return bool
     * 停止服务
     */
    public static function stop(): bool
    {
        $masterPid = Process::getMasterPid();
        $ok = Process::killBySig($masterPid, SIGTERM);
        sleep(3);
        return $ok;
    }
}