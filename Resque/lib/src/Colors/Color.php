<?php
namespace Serve\Colors;

/**
 * Class Color
 * @package Serve\Colors
 * @author twomaio
 */
class Color
{
    public static function println(string $message, string $color = '1;37'): void
    {
        $color = $color ?? '1;37';
        print  "\033[{$color}m {$message} \033[0m" . PHP_EOL;
    }

    public static function print(string $message, string $color = '1;37'): void
    {
        $color = $color ?? '1;37';
        print  "\033[{$color}m {$message} \033[0m";
    }
}