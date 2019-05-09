<?php
namespace Serve\Interfaces;

/**
 * Interface IQueue
 * @package Serve\Interfaces
 * @author twomiao
 * @version v1.0.1
 */
interface IQueue
{
    /**
     * @return mixed
     * 处理队列中业务逻辑
     */
    public function doJob($data);
}