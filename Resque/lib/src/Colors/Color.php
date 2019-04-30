<?php
namespace Serve\Colors;

/**
 * Class Color
 * @package Serve\Colors
 * @author twomaio
 */
class Color
{
    public static function println($title, $color = ''): void
    {
        $color = $color ?? '0m';
        print  "\033[{$color} {$title} \033[0m" . PHP_EOL;
    }

    public static function print($title, $color = ''): void
    {
        $color = $color ?? '0m';
        print  "\033[{$color} {$title} \033[0m";
    }
}
