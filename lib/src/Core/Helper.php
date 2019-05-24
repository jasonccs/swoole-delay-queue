<?php

namespace Serve\Core;

use Swoole\Process;

/**
 * Class Context
 * @package Serve\Core
 * @author twomiao:<995200452@qq.com>
 */
class Helper
{
    /**
     * 守护进程名称
     */
    const PROCESS_NAME = "Serve:Queue %s";

    /**
     * @var string
     * 主进程PID 保存文件
     */
    private static $_pidFile = '/var/run/serve.pid';

    /**
     * @param string $pName
     * @return bool|null
     * 设置进程名
     */
    public static function pname(string $pname): void
    {
        if (hash_equals(strtolower(PHP_OS), 'linux')) {
            $pname = sprintf(static::PROCESS_NAME, $pname);
            if (function_exists('cli_set_process_title')) {
                \cli_set_process_title($pname);
            }
            \swoole_set_process_name($pname);
        }
    }


    /**
     * @param $pid
     * @param $sig
     * @return bool
     * 结束进程
     */
    public static function killed($pid, $sig): bool
    {
        if (Process::kill($pid, 0)) {
            return Process::kill($pid, $sig);
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
     * 存储主进程PID
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