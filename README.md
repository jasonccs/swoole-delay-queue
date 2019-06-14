### Serve 基于Swoole Server 编写的消息队列消费系统
#### 已支持功能:
- 支持数据库操作
- 仅支持Redis 作为消息队列

#### 环境要求：
* PHP >= 7.2
* Swoole >= 4.0.0 (扩展)
* SeasLog >= 2.0.2 (扩展)
* redis 延时组件:<br/>
    Go语言开发的Delayer中间件 下载地址: https://github.com/mix-basic/delayer/releases<br/>
    delayer-client-php 文档地址：https://github.com/mix-basic/delayer-client-php<br/>
 
#### 运行如图：
#####    调试模式 "php serve start"
![image](https://github.com/twomiao/Serve/raw/master/data/start.png "DEBUG运行界面")
##### 守护进程模式 "php serve start -d "
![image](https://github.com/twomiao/Serve/raw/master/data/daemon.png "守护进程")

### Serve-Queue 是什么？
通过Swoole Server 实现消费端并命名为 “Serve-Queue”，Swoole Server API 都可以轻松实现；最新版Swoole 可在多进程worker/task，
添加协程更加高效处理任务。PS, 目前还未使用协程
      

### 核心特点

* 命令行：快速实现消息中间件消费、支持守护进程、常驻内存；
* 自动加载：基于 PSR-4 ，完全使用 Composer 构建；
* 模块化：支持 Composer ，可以很方便的使用第三方库；

### Install

```
  $> git clone https://github.com/twomiao/Serve.git
  $> cd Serve/
  $> composer install
```

### Run
```
  $> cd Serve/
  $> chmod 777 ./serve
  $> php serve start
```

### Serve-Queue [Options]

- start: 调试模式运行 [ 额外打印所有信息到终端];
- start -d: 守护进程运行;
- reload: 平滑重启task进程 [Job::doJob];
- stop: 停止运行Serve-Queue;
- reload:all: 平滑重启Worker 和 Task 进程;

### Job Code

```
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
     * @return string|null 
     * null: 不执行after();
     * string: 执行after();
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
     * 此方法不支持平滑重启
     */
    public function after($data)
    {
        // 带颜色的输出 - 紫色
//        Color::println("哈哈哈哈哈",ColorText::FG_LIGHT_PURPLE);
        // 记录日志
        Log::debug($data);
    }
}
```
