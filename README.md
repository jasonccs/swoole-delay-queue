### Serve 基于Swoole Server 编写的消息队列消费系统
#### 已支持功能:

- 目前通过./serve start 命令启动
- 守护进程化、打开app/config/Serve.ini [Swoole] daemonize => true 即可
- 系统已上线、可以通过命令：./serve reload 或者 reload:all 更新业务代码
- 支持数据库操作、目前消息端暂只支持Redis List
- 后期支持Kafaka、RabbitMQ，还待改进

##### 提醒：
 - vim ./serve文件,第一行脚本声明改为你的PHP.exe位置。举个例子：/usr/local/php/bin/*****
 - 待后更新 .....
##### 运行效果图：
ncopy在CODE上查看代码片派生到我的代码片 
![Serve](http://www.baiud.com/baidu.logo "Serve运行效果")  
    
```
[root@VM_146_146_centos app]# ./serve start
[ INFO ] 2019-04-23 22:14:51 Business server started, Master pid is: 25713.
[ √ ] TaskWorker:9 started.
[ √ ] TaskWorker:8 started.
[ √ ] TaskWorker:11 started.
[ √ ] TaskWorker:10 started.
[ √ ] TaskWorker:12 started.
[ √ ] TaskWorker:13 started.
[ √ ] Worker:0 started.
[ √ ] TaskWorker:14 started.
[ √ ] Worker:1 started.
[ √ ] TaskWorker:15 started.
[ √ ] Worker:4 started.
[ √ ] Worker:2 started.
[ √ ] Worker:6 started.
[ √ ] Worker:3 started.
[ √ ] Worker:7 started.
[ √ ] Worker:5 started.
处理业务逻辑 ...
处理业务逻辑 ...
处理业务逻辑 ...
处理业务逻辑 ...
处理业务逻辑 ...
处理业务逻辑 ...
处理业务逻辑 ...
```


# How to use?

### Install

```
git clone https://github.com/twomiao/Serve.git
```

### Job Code

```php
namespace app\service;

use app\db\Order;

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
        print "处理业务逻辑 ...\n";
        // 待支付订单,直接取消关闭
        $orderSn = $data['orderSn'];
        (new Order($pdo))->getOrder($orderSn)->cancelled();
    }
}
```

### Run
> 提醒：更改为自己的PHP版本
> vim serve.php #!/usr/local/*** 

```
./serve start/stop/reload/reload:all
```

### Serve 启动参数说明

- start: 启动 task 和 worker 进程
- reload: 默认只更新task 业务代码 (Job::business)
- stop: 停止Serve 消费端
- reload:all: 热更新全部业务代码

