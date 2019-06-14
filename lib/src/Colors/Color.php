<?php
namespace Serve\Colors;

/**
 * Class Color
 * @package Serve\Colors
 * @author twomiao:<995200452@qq.com>
 */
class Color
{
    public static function println(string $message, string $color = ColorText::FG_WHITE): void
    {
        print  "\033[{$color}m {$message} \033[0m" . PHP_EOL;
    }

    public static function print(string $message, string $color = ColorText::FG_WHITE): void
    {
        print  "\033[{$color}m {$message} \033[0m";
    }
}