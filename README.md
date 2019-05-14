### Serve 基于Swoole Server 编写的消息队列消费系统
#### 已支持功能:
- 支持数据库操作
- 仅支持Redis 作为延时队列

#### 环境要求：
* PHP >= 7.2
* Swoole >= 4.0.0 提醒: Swoole 版本应该要求更低,但尽量升级到最新版吧！
* redis 延时特性:<br/>
    Delayer 延时中间件下载: wget https://github.com/mix-basic/delayer/releases<br/>
    Delayer PHP客户端具体详情：https://github.com/mix-basic/delayer-client-php<br/>
 
#### 运行如图：
##### 启动命令 "php serve start"
![image](https://github.com/twomiao/Serve/raw/master/img/test.png "启动界面")
![image](https://github.com/twomiao/Serve/raw/master/img/start.png "消费处理中")
![image](https://github.com/twomiao/Serve/raw/master/img/daemon.png "守护进程结构")
##### 平滑重启命令 “php serve reload”,新增加文字提醒 “233333”
![image](https://github.com/twomiao/Serve/raw/master/img/reload.png "未平滑重启")
![image](https://github.com/twomiao/Serve/raw/master/img/reload02.png "已平滑重启")

# How to use?

### Install

```
git clone https://github.com/twomiao/Serve.git
```

### Run
```
chmod 777 ./serve
php serve start
```

### Serve 启动参数说明

- start: 启动 Task 和 Worker进程
- reload: 只更新Task 业务代码 (Job::doJob)
- stop: 停止Serve 服务运行
- reload:all: 平滑重启Worker 和 Task

### Job Code

```php
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
         }
        */
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
//        print "2333\n";
        // TODO: Implement finish() method.
    }
}
```
