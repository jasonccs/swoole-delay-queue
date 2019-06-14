<?php
/****************************************************************
 * worker_num | crontab  | task_worker_num | task_max_request   *
 * **************************************************************
 * worker数量 | 扫描速度 | task 启动数量   | 每个task 完成任务数*
 * **************************************************************
 */
return [
    'swoole' => [
        'host' => '127.0.0.1',
        'port' => 9501,
        'worker_num' => 8,          // 启动worker 进程8个
        'crontab' => 350,           // 扫描队列速度,350毫秒一次
        'task_worker_num' => 12,     // 启动task 进程8个
        'task_max_request' => 5000, // 每个task 处理5个任务退出，防止内存泄漏
                                    // 默认task:5000, 值过小不好,会导致CPU过于频繁使用,TIME_WAIT 大量出现
        'log_dir' => '/log/'        // 日志目录,一般不用改
    ],
    'redis' => [
        'host' => '127.0.0.1',
        'port' => 6379,
        'password' => '',
        'database' => 0,
    ],
    'mysql' => [
        'type' => 'mysql',
        'host' => '127.0.0.1',
        'port' => 3306,
        'user' => 'root',
        'password' => 'pengyun120',
        'database' => 'mysql',
        'charset' => 'utf-8',
        'prefix' => '' // 表前缀
    ],
];