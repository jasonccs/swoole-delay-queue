<?php

namespace app\job;

use app\db\Order;
use app\util\MailBox;
use app\util\Sms;
use app\util\WxMessage;
use app\util\WxPay;
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
     * @var string
     * 注意: 表前缀问题
     */
    private $tableName = 'order';

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
     * @param $pdo 数据库客户端句柄
     * @param $data getData() 取出来的数据
     * 业务逻辑操作
     */
    public function doJob($db, $data): void
    {
//        var_dump($data);
        switch ($data['action']) {
            // 自动取消订单
            case Action::CANCELLED:
                //  待支付订单,直接取消关闭
                (new Order($db))->getOrder($data['order_sn'])->cancelled();
                break;
            case Action::SEND_EMAIL:
                MailBox::send(); // 邮件定时发送
                break;
            default:
                // 其它操作
                break;
        }
        /* 演示db()函数数据库操作使用
    if ($data !== false)
      {
          $order = db()->get($this->tableName, ['id', 'order_stats', 'order_sn', 'create_at'],
              [
                  "{$this->tableName}.id" => 1
              ]
          );
          var_dump($order);
      }*/
    }

    /**
     * @param $data
     * @return mixed
     * swoole url:https://wiki.swoole.com/wiki/page/135.html
     * 注意:平滑重启不支持finish方法
     */
    public function finish($data)
    {
        Log::debug($data);
//        print "233333333333333333333\n";
        // TODO: Implement finish() method.
    }
}