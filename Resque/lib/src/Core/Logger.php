<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/4 0004
 * Time: 下午 23:24
 */

namespace Serve\Core;

use Serve\Colors\Color;
use Serve\Colors\ColorText;
use Serve\Interfaces\LoggerInterface;

abstract class Logger implements LoggerInterface
{
    private static $seaslog = false;

    public static function init()
    {
        if (class_exists('SeasLog')) {
            self::$seaslog = true;
            $logDir = dirname(env('swoole.log_dir'));
            \SeasLog::setBasePath($logDir);
            \SeasLog::setLogger('log');
            @chmod($logDir, 0777);
        }
    }

    public static function debug(string $message, array $data = array())
    {
        if (self::$seaslog) {
            \SeasLog::debug($message, $data);
        }
    }

    public static function info(string $message, $data = array())
    {
        if (self::$seaslog) {
            \SeasLog::info($message, $data);
        }
    }

    public static function alert(string $message, array $data = [])
    {
        if (self::$seaslog) {
            \SeasLog::alert($message, $data);
        }
    }

    public static function error(string $message)
    {
        if (self::$seaslog) {
            \SeasLog::error($message);
        }
    }

    public static function notice(string $message, $info = true)
    {
        if ($info) {
            $title = "[ INFO ] " . date('Y-m-d H:i:s')  . " {$message}";
            Color::println($title, ColorText::BLUE_FONT);
            return;
        } else {
            $title = "[WARNING] " . date('Y-m-d H:i:s') .  " {$message}";
            Color::println($title, ColorText::RED_FONT);
        }
    }
}