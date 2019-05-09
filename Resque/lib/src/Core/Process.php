<?php
namespace Serve\Core;


/**
 * Class Process
 * @package Serve\Core
 * @author twomiao
 * @version v1.0.1
 */
class Process extends \Swoole\Process
{
    /**
     * 守护进程名称
     */
    const PROCESS_NAME_PREFIX = "Serve:Redis %s";

    /**
     * @var string
     * 主进程PID 保存文件
     */
    private static $_pidFile = '/var/run/serve.pid';

    /**
     * @param string $processName
     * @return bool|null
     * 设置守护进程名称
     */
    public static function daemonize(string $processName): ?bool
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

    /**
     * @return int
     * 读取主进程PID
     */
    public static function getMasterPid(): int
    {
        $masterPid = @file_get_contents(self::$_pidFile);
        if (intval($masterPid) > 0 && is_numeric($masterPid)) {
            if (Process::kill($masterPid, 0)) {
                return $masterPid;
            }
        }
        return 0;
    }

    /**
     * @param int $masterPid
     * @return int|null
     * 保存主进程PID
     */
    public static function setMasterPid(int $masterPid): ?int
    {
        $dir = dirname(self::$_pidFile);
        if (!is_dir($dir)) {
            @mkdir($dir, 0777, true);
        } else {
            @chmod($dir, 0777);
        }
        // pid 目录不存在就创建,当然这个目录不可能不存在
        return @file_put_contents(self::$_pidFile, $masterPid);
    }
}