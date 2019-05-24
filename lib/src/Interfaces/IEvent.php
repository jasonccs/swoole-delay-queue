<?php
namespace Serve\Interfaces;

/**
 * Interface IEvent
 * @package Serve\Interfaces
 * @author twomiao:<995200452@qq.com>
 */
interface IEvent
{
    public function onStart($serve);

    public function onWorkerStart($server, $workerId);

    public function onTask($server, $taskId, $reactorId, $data);

    public function onFinish($server, $taskId, $data);
}