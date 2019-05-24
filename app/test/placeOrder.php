<?php
require_once dirname(__DIR__) . '/../vendor/autoload.php';

use Serve\Core\Env;
use Serve\Core\RedisClient;
use Serve\Core\MysqlClient;
use app\Services\Order;
use Delayer\Message;

defined('APP_PATH') or define('APP_PATH', dirname(__DIR__));
defined('DS') or define('DS', DIRECTORY_SEPARATOR);

$len = isset($argv[1]) ? $argv[1] : 1;
$action = isset($argv[2]) ? $argv[2] : 'order';
Env::load();

$client = RedisClient::makeInstance();
$mysql = MysqlClient::makeInstance();

for ($i = 1; $i <= $len; $i++) {
    switch ($action) {
        // 定时邮件发送
        case 'email':
            $toUser = ['马云', '马化腾', '火柴人'];
            $fromUser = ['张三', '李四', '王五'];
            $data = [
                'fom_user' => $fromUser[array_rand($fromUser)],
                'to_user' => $toUser[array_rand($toUser)],
                'create_at' => time(),
                'created_at' => date('Y-m-d H:i:s'),
                'action' => 1
            ];
            message($client, $data);
            break;
        // 订单延时关闭
        case 'order':
            $data = [
                'order_sn' => '2018101712578956648' . mt_rand(100000, 900000),
                'order_stats' => Order::UNPAID_STATUS,
                'create_at' => time(),
                'created_at' => date('Y-m-d H:i:s'),
                'action' => -1 // 自动取消操作
            ];
            message($client, $data);
            unset($data['action']);
            $res = $mysql->insert("order", $data);
            break;
    }

}

/**
 * @param $client
 * @param $data
 * @return mixed
 * 消息
 */
function message($client, $data)
{
    $message = new Message([
        // 任务ID，必须全局唯一
        'id' => md5(uniqid(mt_rand(), true)),
        // 主题，取出任务时需使用
        'topic' => 'delayer::order_queue',
        // 必须转换为string类型
        'body' => json_encode($data, JSON_UNESCAPED_UNICODE),
    ]);
    return $client->push($message, 3, 604800);
}

