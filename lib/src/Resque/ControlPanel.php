<?php

namespace Serve\Resque;

use Serve\Core\Helper;

/**
 * Class ControlPanel
 * @package Serve\Resque
 * @author twomiao:<995200452@qq.com>
 */
class ControlPanel
{
    /**
     * @return bool
     * 服务是否已运行
     */
    public static function isRunning(): bool
    {
        $masterPid = Helper::getMasterPid();
        if (intval($masterPid) > 0 && is_numeric($masterPid)) {
            if (Helper::killed($masterPid, 0)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return bool
     * 热更新Task 和 Worker
     */
    public static function reloadAll(): bool
    {
        $masterPid = Helper::getMasterPid();
        if ($masterPid > 0) {
            return Helper::killed($masterPid, SIGUSR1);
        }
        return false;
    }

    /**
     * @return bool
     * 热更新 task 进程
     */
    public static function reloadTask(): bool
    {
        $masterPid = Helper::getMasterPid();
        if ($masterPid > 0) {
            return Helper::killed($masterPid, SIGUSR2);
        }
        return false;
    }

    /**
     * @return bool
     * 停止服务
     */
    public static function stop(): bool
    {
        $masterPid = Helper::getMasterPid();
        $ok = Helper::killed($masterPid, SIGTERM);
        sleep(3);
        return $ok;
    }
}