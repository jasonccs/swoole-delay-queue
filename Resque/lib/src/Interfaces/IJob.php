<?php

namespace Serve\Interfaces;

/**
 * Interface IJob
 * @package Serve\Interfaces
 * @author twomiao
 * @version v1.0.1
 */
interface IJob
{
    /**
     * @param $queue
     * @return string|null
     * Redis 延时队列获取数据返回给Serve处理
     * 注意: 一般不需要更改
     */
    public function getData($queue): ?string;

    /**
     * @param $pdo 数据库客户端句柄
     * @param $data getData() 取出来的数据
     * 业务逻辑操作
     */
    public function doJob($pdo, $data);

    /**
     * @param $data
     * @return mixed
     * doJob处理完成后,接下来后面的工作
     * swoole url:https://wiki.swoole.com/wiki/page/135.html
     */
    public function finish($data);
}