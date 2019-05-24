<?php

namespace app\Job;

use app\Services\Order;
use app\Utils\MailBox;
use app\Utils\Sms;
use app\Utils\WxMessage;
use app\Utils\WxPay;
use Serve\Colors\Color;
use Serve\Colors\ColorText;
use Serve\Core\Log;
use Serve\Interfaces\IJob;


class Job implements IJob
{
    /**
     * @var string
     *
     */
    private $queue = 'delayer::order_queue';

    /**
     * @param $queue
     * @return string|null
     * Redis 延时队列获取数据返回给Serve处理
     * 注意: 一般不需要更改
     */
    public function getData($queue): ?string
    {
        $message = $queue->bPop($this->queue, 2);
        if ($message !== false) {
            return $message->body;
        }
        return null;
    }

    /**
     * @param $db
     * @param array $data
     * @return string|null
     */
    public function doJob(array $data): ?string
    {
        $job = "task (".getmypid()."), succeeded ({$data['order_sn']}).";

        // 举例子：带颜色的打印信息
//        Color::println("2333, 平滑重启成功", ColorText::FG_LIGHT_RED);

        switch ($data['action']) {
            // 超时自动取消订单
            case Action::ORDER_CANCELLED:
                // 15分钟未支付,自动关闭订单
                (new Order())->getOrder($data['order_sn'])->cancelled();
                // 通过 return 把 $data 传递给FINISH. -> 具体看SWOOLE 文档哈
                return $job;
            case Action::MAILER_SEND:
                MailBox::send(); // 邮件定时发送
                break;
            default:
                // 其它操作
                break;
        }
        // 提醒：如果不想要调用Finish 方法,返回null
        /* 演示Db()函数数据库操作使用
         if ($data !== false)
           {
               $order = Db()->get($this->tableName, ['id', 'order_stats', 'order_sn', 'create_at'],
                   [
                       "{$this->tableName}.id" => 1
                   ]
               );
               var_dump($order);
           }*/
        return null;
    }

    /**
     * @param $data
     * @return mixed
     * swoole url:https://wiki.swoole.com/wiki/page/135.html
     * 注意:平滑重启不支持finish方法
     */
    public function finish($data)
    {
        if (!empty($data)) {
            Color::println(
                sprintf(date('Y-m-d H:i:s') . ' %s', $data)
                , ColorText::FG_LIGHT_PURPLE);
        }
        // TODO: Implement finish() method.
    }
}