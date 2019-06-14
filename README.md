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
![image](https://github.com/twomiao/Serve/raw/master/data/start.png "DEBUG运行界面")
![image](https://github.com/twomiao/Serve/raw/master/data/test.png "处理业务阶段")
##### 平滑重启命令 “php serve reload”,新增加文字提醒 “2333, 平滑重启成功”
![image](https://github.com/twomiao/Serve/raw/master/data/reload.png "平滑重启命令")
![image](https://github.com/twomiao/Serve/raw/master/data/reload02.png "平滑重启")
![image](https://github.com/twomiao/Serve/raw/master/data/daemon.png "守护进程运行界面")
![image](https://github.com/twomiao/Serve/raw/master/data/daemon02.png "守护进程")

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
```
