<?php
namespace Serve\Core;

/**
 * Class RedisClient
 * @package Serve\Core
 * @author twomiao
 */
class RedisClient
{
    private static $delayRedis = null;

    public function make(): ?\Delayer\Client
    {
        if (empty(static::$delayRedis))
        {
            static::$delayRedis = new \Delayer\Client([
                'host' => env('redis.host'),
                'port' => env('redis.port'),
                'password' => env('redis.password'),
                'database' => env('redis.database'),
            ]);
        }
        return  self::$delayRedis;
    }
}