<?php

/**
 *  Serve 下面也依赖这个函数
 *  切记不可删除,否则会运行失败
 *  这样写是为了演示案例,这个地方可以自定义函数
 *  env => 环境配置读取函数
 */
if (!function_exists('env')) {
    function env($name)
    {
        return \Serve\Core\Env::get($name) ?? null;
    }
}

/**
 *  Serve 下面也依赖这个函数
 *  切记不可删除,否则会运行失败
 *  这样写是为了演示案例,这个地方可以自定义函数
 *  db => 数据库客户端,操作数据库
 */
if (!function_exists('db')) {
    function db($db = 'mysql')
    {
        return \Serve\Core\ClientFactory::makeClient($db);
    }
}