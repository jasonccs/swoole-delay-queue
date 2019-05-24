<?php
namespace Serve\Core;

use Delayer\Client;

/**
 * Class RedisClient
 * @package Serve\Core
 * @author twomiao <995200452@qq.com>
 */
class RedisClient
{
    private static $delayRedis = null;

    private function __construct()
    {
    }

    public static function makeInstance(): ?Client
    {
        if (empty(static::$delayRedis))
        {
            static::$delayRedis = new Client([
                'host' => env('redis.host'),
                'port' => env('redis.port'),
                'password' => env('redis.password'),
                'database' => env('redis.database'),
            ]);
        }
        return  self::$delayRedis;
    }

    private function __clone()
    {
    }
}