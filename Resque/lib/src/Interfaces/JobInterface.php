<?php
namespace Serve\Interfaces;


/**
 * Interface JobInterface
 * @package Resque\Interfaces
 * 处理任务的接口
 */
interface JobInterface
{
    /**
     * @param $server
     * @param $taskId
     * @param $reactorId
     * @param $data 队列中拿到的数据
     * @return mixed
     * 处理业务逻辑
     */
    public function business($server, $taskId, $reactorId, $data);

    public function dequeue(): string;
}