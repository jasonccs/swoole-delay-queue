<?php

namespace app\Job;

use app\Services\Order;
use Serve\Colors\Color;
use Serve\Colors\ColorText;
use Serve\Core\Log;
use Serve\Interfaces\IJob;

class Job implements IJob
{
    /**
     * @param array $data
     * @param \Serve\Interfaces\mysql $db
     * @return string|null 传递任务给FINISH，做后续处理
     */
    public function doJob(array $data, $db): ?string
    {
        $job = "task (".getmypid()."), succeeded ({$data['order_sn']}).";

        // 举栗子：带颜色的打印信息
        Color::println("2333, 平滑重启成功", ColorText::FG_LIGHT_RED);

        switch ($data['action']) {
            // 超时自动取消订单
            case Action::ORDER_CANCELLED:
                // 订单关闭逻辑
//                (new Order())->getOrder($data['order_sn'])->cancelled();
                return $job;
            case Action::MAILER_SEND:
                // 邮件发送逻辑
                break;
            default:
                // 其它操作
                break;
        }
        return null;
    }

    /**
     * @param $data
     * @return mixed
     * Swoole 文档地址:https://wiki.swoole.com/wiki/page/135.html
     * 提醒:平滑重启不支持finish方法
     */
    public function after($data)
    {
        // 带颜色的输出 - 紫色
//        Color::println("哈哈哈哈哈",ColorText::FG_LIGHT_PURPLE);
        // 记录日志
        Log::debug($data);
    }
}