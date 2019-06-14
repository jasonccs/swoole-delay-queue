<?php

namespace Serve\Interfaces;

/**
 * Interface IJob
 * @package Serve\Interfaces
 * @author twomiao:<995200452@qq.com>
 */
interface IJob
{
    /**
     * @param array $data  队列中的数据
     * @param $db mysql 客户端
     * @return string|null
     * 处理自己的业务逻辑
     */
    public function doJob(array $data, $db): ?string;

    /**
     * @param string $data
     * @return mixed
     * doJob处理完成后,接下来后面的工作
     * swoole 文档地址:https://wiki.swoole.com/wiki/page/135.html
     */
    public function after(string $data);
}