### Serve 基于Swoole Server 编写的消息队列消费系统
#### 已支持功能:
- 支持数据库操作、目前消息队列客户端端暂只支持Redis List
- 后期支持Kafaka、RabbitMQ，还待改进 ....

##### 环境要求：
 - PHP v7.3.4 和 Swoole v4.3.3
 - redis 延时特性, Linux 下载地址 wget https://github.com/mix-basic/delayer/releases
 - 待后更新 .....
 
#### 运行效果：
- 启动命令 "php serve start"
![image](https://github.com/twomiao/Serve/raw/master/img/start.png "启动画面")
![image](https://github.com/twomiao/Serve/raw/master/img/test.png "热更新代码前")
- 热更新命令 "php serve reload" pS: 会发现少了“处理业务逻辑...”,说明代码生效
![image](https://github.com/twomiao/Serve/raw/master/img/reload.png "热更新命令执行后")
![image](https://github.com/twomiao/Serve/raw/master/img/reload02.png "热更新代码后")

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

- start: 启动 task 和 worker 进程
- reload: 默认只更新task 业务代码 (Job::business)
- stop: 停止Serve 消费端
- reload:all: 热更新全部业务代码

### Job Code

```php
namespace app\service;

use app\db\Order;
use Serve\Colors\Color;
use Serve\Colors\ColorText;

class Job
{
    /**
     * @var string
     *
     */
    private $queue = 'delayer::order_queue';

    /**
     * @param $server
     * @return string
     * 消息队列中拿取数据
     */
    public function dequeue($queue): ?string
    {
        $message = $queue->bPop($this->queue, 2);
        if ($message !== false) {
            return $message->body;
        }
        return null;
    }

    /**
     * @param $server
     * @param $taskId
     * @param $reactorId
     * @param $data
     * 队列中拿到的数据,进行业务逻辑操作
     */
    public function business($pdo, $data): void
    {
        Color::println("处理业务逻辑 ...", ColorText::YELLOW_FONT);
//         待支付订单,直接取消关闭
        $orderSn = $data['orderSn'];
        (new Order($pdo))->getOrder($orderSn)->cancelled();
    }
}
```
