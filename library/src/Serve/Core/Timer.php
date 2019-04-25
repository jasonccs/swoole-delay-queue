<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/31 0031
 * Time: 下午 21:59
 */

namespace Serve\Core;

/**
 * Class ShotClock
 * @package logic\Lib
 * 秒计时器
 */
class Timer
{
    /**
     * @var $timerId
     * 计时器ID
     */
    private static $timerId;

    /**
     * @param callable $callable
     * @param int $seconds 间隔执行时间
     * @return ShotClock 计时器对象
     * 计时器任务执行
     */
    public static function interval(callable $callable, $microtime = 2000)
    {
        self::$timerId = \swoole_timer_tick($microtime, $callable);
        $callable(self::$timerId);
        return new self;
    }

    /**
     * @param $timerId
     * @return mixed
     * 用于计时器内部关闭
     */
    public static function clear($timerId)
    {
        return \swoole_timer_clear($timerId);
    }

    /**
     * @return bool
     * 用于计时器外部关闭
     */
    public function stop()
    {
        return \swoole_timer_clear(self::$timerId);
    }
}