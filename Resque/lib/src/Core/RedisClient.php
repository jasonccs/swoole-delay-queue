<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/6 0006
 * Time: 下午 22:21
 */

namespace Serve\Core;

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