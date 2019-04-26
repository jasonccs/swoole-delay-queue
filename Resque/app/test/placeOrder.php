<?php
require_once dirname(__DIR__) . '/../vendor/autoload.php';

defined ('APP_PATH') or define('APP_PATH', dirname(__DIR__));
defined ('DS') or define('DS', DIRECTORY_SEPARATOR);

$len = isset($argv[1]) ? $argv[1] : 1;
\Serve\Core\Env::load();

$client = \Serve\Core\ClientFactory::makeClient('redis');
$mysql = \Serve\Core\ClientFactory::makeClient('mysql');


for ($i = 1; $i <= $len; $i++) {
    $data = [
        'orderSn'    => '2018101712578956648' . mt_rand(100000, 900000),
        'orderStats' => \app\db\Order::UNPAID_STATUS,
        'create_at'  => time(),
        'created_at' => date('Y-m-d H:i:s')
    ];
    $message = new \Delayer\Message([
        // 任务ID，必须全局唯一
        'id' => md5(uniqid(mt_rand(), true)),
        // 主题，取出任务时需使用
        'topic' => 'delayer::order_queue',
        // 必须转换为string类型
        'body' => json_encode($data),
    ]);
    $ret = $client->push($message, 3, 604800);
    if ($ret !== false) {
        $res = $mysql->insert("order", $data);
    }
}




