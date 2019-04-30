<?php
namespace Serve\Interfaces;

interface EventInterface
{
    public function onWorkerStart($server, $workerId);

    public function onTask($server, $taskId, $reactorId, $data);

    public function onFinish($server, $taskId, $data);
}