<?php
namespace Serve\Core;


class ProcessHelper
{
    const PROCESS_NAME_PREFIX = "redis:queue %s";

    public static function setProcessName(string $processName): ?bool
    {
        $processName = sprintf(self::PROCESS_NAME_PREFIX, $processName);
        if (function_exists('cli_set_process_title')) {
            \cli_set_process_title($processName);
            return true;
        }
        \swoole_set_process_name($processName);
    }

    /**
     * @param $pid
     * @param $sig
     * @return mixed
     * 通过进程信号管理进程
     */
    public static function killBySig($pid, $sig): bool
    {
        if (\Swoole\Process::kill($pid, 0)) {
            return \Swoole\Process::kill($pid, $sig);
        }
        return false;
    }

    public static function saveMasterPid(int $masterPid): ?int
    {
        $dir = dirname(self::getMasterPidFile());
        if (!is_dir($dir)) {
            @mkdir($dir, 0777, true);
        } else {
            @chmod($dir, 0777);
        }
        return @file_put_contents(self::getMasterPidFile(), $masterPid);
    }

    /**
     * @return false|string
     * 获取主进程PID位置
     */
    public static function getMasterPidFile() {
        return env('swoole.master_pid_file');
    }
}