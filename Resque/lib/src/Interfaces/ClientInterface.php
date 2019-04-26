<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/6 0006
 * Time: 下午 22:16
 */

namespace Serve\Interfaces;


interface ClientInterface
{
    public static function makeClient(string $name);
}