<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/25 0025
 * Time: 上午 9:57
 */

namespace Serve\Colors;

class Color
{
    public static function println(string $title, string $color = ''): void
    {
        $color = $color ?? '0m';
        print  "\033[{$color} {$title} \033[0m" . PHP_EOL;
    }

    public static function print(string $title, string $color = ''): void
    {
        $color = $color ?? '0m';
        print  "\033[{$color} {$title} \033[0m";
    }
}
