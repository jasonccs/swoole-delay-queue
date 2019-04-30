<?php
namespace Serve\Core;

/**
 * Class RedisClient
 * @package Serve\Core
 * @author twomiao
 */
class RedisClient
{
    private $delayRedis = null;

    public function make(): ?\Delayer\Client
    {
        $this->delayRedis = new \Delayer\Client([
            'host' => env('redis.host'),
            'port' => env('redis.port'),
            'password' => env('redis.password'),
            'database' => env('redis.database'),
        ]);
        return $this->delayRedis;
    }
}