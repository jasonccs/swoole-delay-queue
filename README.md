### Serve 基于Swoole Server 编写的消息队列消费系统
#### 已支持功能:
- 支持数据库操作
- 仅支持Redis 作为延时队列

#### 环境要求：
* PHP >= 7.2
* Swoole >= 4.0.0 提醒: Swoole 版本应该要求更低,但尽量升级到最新版吧！ (扩展)
* SeasLog (扩展)
* redis 延时特性:<br/>
    Delayer 延时中间件下载: wget https://github.com/mix-basic/delayer/releases<br/>
    Delayer PHP客户端具体详情：https://github.com/mix-basic/delayer-client-php<br/>
 
#### 运行如图：
##### 启动命令 "php serve start"
![image](https://github.com/twomiao/Serve/raw/master/data/start.png "启动界面")
![image](https://github.com/twomiao/Serve/raw/master/data/test.png "消费处理中")
![image](https://github.com/twomiao/Serve/raw/master/data/daemon.png "守护进程结构")
##### 平滑重启命令 “php serve reload”,新增加文字提醒 “2333, 平滑重启成功”
![image](https://github.com/twomiao/Serve/raw/master/data/reload.png "未平滑重启")
![image](https://github.com/twomiao/Serve/raw/master/data/reload02.png "已平滑重启")

# How to use?

### Install

```
  git clone https://github.com/twomiao/Serve.git
  composer install
```

### Run
```
  $> cd home/Serve/
  $> chmod 777 ./serve
  $> php serve start
```

### Serve 启动参数说明

- start: 启动 Serve Queue 消费
- start -d: 守护进程运行
- reload: 只更新Task 业务代码 (Job::doJob)
- stop: 停止Serve 服务运行
- reload:all: 平滑重启Worker 和 Task

### Job Code

```php
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
//        Color::println("233333333333333333333", ColorText::FG_LIGHT_RED);

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
```
