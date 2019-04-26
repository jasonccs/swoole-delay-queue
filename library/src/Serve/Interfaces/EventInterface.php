<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/26 0026
 * Time: 上午 0:18
 */

namespace Serve\Interfaces;

interface EventInterface
{
    public function onWorkerStart($server, $workerId);

    public function onTask($server, $taskId, $reactorId, $data);

    public function onFinish($server, $taskId, $data);
}