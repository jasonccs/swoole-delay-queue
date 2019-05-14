<?php

namespace Serve\Core;

use Serve\Colors\Color;
use Serve\Colors\ColorText;

/**
 * Class Check
 * @package Serve\Core
 * @author twomiao
 * 环境检测是否通过
 */
class Check
{
    public static function pass(): void
    {
        if (PHP_SAPI !== 'cli') {
            exit('CLi run!' . PHP_EOL);
        }

        if (version_compare(PHP_VERSION, '7.2.0', '<')) {
            exit('Your current PHP version is ' . PHP_VERSION . ', and requires >= 7.2' . PHP_EOL);
        }

        if (version_compare(SWOOLE_VERSION, '4.0.0', '<')) {
            exit('Your current Swoole version is ' . SWOOLE_VERSION . ', and requires >= 4.0.0' . PHP_EOL);
        }
    }
}
