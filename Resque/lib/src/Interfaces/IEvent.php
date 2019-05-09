<?php
namespace Serve\Interfaces;

interface IEvent
{
    public function onWorkerStart($server, $workerId);

    public function onTask($server, $taskId, $reactorId, $data);

    public function onFinish($server, $taskId, $data);
}