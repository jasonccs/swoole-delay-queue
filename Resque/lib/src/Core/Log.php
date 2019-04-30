<?php
namespace Serve\Core;

use Serve\Colors\Color;
use Serve\Colors\ColorText;
use Serve\Interfaces\LoggerInterface;

use SeasLog;

/**
 * Class Log
 * @package Serve\Core
 * @author twomiao
 */
class Log
{
    private static $seaslog = false;

    //设置日志目录
    public static function init()
    {
        if (class_exists('SeasLog')) {
            $logDir = self::getLogDir();
            self::$seaslog = true;
            SeasLog::setBasePath($logDir);
        }
    }

    //代理seaglog的静态方法，如 SeasLog::debug
    public static function __callStatic($name, $arguments)
    {
        if (self::$seaslog) {
            forward_static_call_array(['SeasLog', $name], $arguments);
        }
    }

    public static function getLogDir()
    {
        return APP_PATH . env('swoole.log_dir');
    }

    /**
     * 记录debug日志
     *
     * @param string|array $message
     * @param array $context
     * @param string $module
     */
    public static function debug($message, array $context = array(), $module = '')
    {
        Color::print(self::setTextFormat('DEBUG'), ColorText::FG_YELLOW);
        Color::println($message, ColorText::FG_GREEN);

        #$level = SEASLOG_DEBUG
        if (self::$seaslog) {
            SeasLog::debug($message, $context, $module);
        }
    }

    /**
     * 记录info日志
     *
     * @param string|array $message
     * @param array $context
     * @param string $module
     */
    public static function info($message, array $context = array(), $module = '')
    {
        Color::println(self::setTextFormat('INFO', $message), ColorText::FG_BLUE);
        #$level = SEASLOG_INFO
        if (self::$seaslog) {
            SeasLog::info($message, $context, $module);
        }
    }

    private static function setTextFormat($level, $text = ''): string
    {
        return sprintf("%s [ {$level} ] => {$text}", date('Y-m-d H:i:s'));
    }

    /**
     * 记录notice日志
     *
     * @param string|array $message
     * @param array $context
     * @param string $module
     */
    public static function notice($message, array $context = array(), $module = '')
    {
        #$level = SEASLOG_NOTICE
        if (self::$seaslog) {
            SeasLog::notice($message, $context, $module);
        }
    }

    /**
     * 记录warning日志
     *
     * @param string|array $message
     * @param array $context
     * @param string $module
     */
    public static function warning($message, array $context = array(), $module = '')
    {
        Color::println(self::setTextFormat('WARNING', $message), ColorText::FG_RED);
        #$level = SEASLOG_WARNING
        if (self::$seaslog) {
            SeasLog::warning($message, $context, $module);
        }
    }

    /**
     * 记录error日志
     *
     * @param string|array $message
     * @param array $context
     * @param string $module
     */
    public static function error($message, array $context = array(), $module = '')
    {
        Color::println(self::setTextFormat("ERROR", $message), ColorText::FG_RED);
        #$level = SEASLOG_ERROR
        if (self::$seaslog) {
            SeasLog::error($message, $context, $module);
        }
    }

    /**
     * 记录critical日志
     *
     * @param string|array $message
     * @param array $context
     * @param string $module
     */
    public static function critical($message, array $context = array(), $module = '')
    {
        #$level = SEASLOG_CRITICAL
        if (self::$seaslog) {
            SeasLog::critical($message, $context, $module);
        }
    }

    /**
     * 记录alert日志
     *
     * @param string|array $message
     * @param array $context
     * @param string $module
     */
    public static function alert($message, array $context = array(), $module = '')
    {
        #$level = SEASLOG_ALERT
        if (self::$seaslog) {
            SeasLog::alert($message, $context, $module);
        }
    }

    /**
     * 记录emergency日志
     *
     * @param string|array $message
     * @param array $context
     * @param string $module
     */
    public static function emergency($message, array $context = array(), $module = '')
    {
        #$level = SEASLOG_EMERGENCY
        if (self::$seaslog) {
            SeasLog::emergency($message, $context, $module);
        }
    }

    /**
     * 通用日志方法
     *
     * @param              $level
     * @param string|array $message
     * @param array $context
     * @param string $module
     */
    public static function log($level, $message, array $context = array(), $module = '')
    {
        if (self::$seaslog) {
            SeasLog::log($level, $message, $context, $module);
        }
    }

    /**
     * @param \Throwable $e
     * @desc 输出异常日志
     */
    public static function exception(\Throwable $e)
    {
        $array = [
            '{file}' => $e->getFile(),
            '{line}' => $e->getLine(),
            '{code}' => $e->getCode(),
            '{message}' => $e->getMessage(),
            '{trace}' => $e->getTraceAsString(),
        ];
        $message = implode(' | ', array_keys($array));
        self::emergency($message, $array);
    }
}