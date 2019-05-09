<?php
return [
    'swoole' => [
        'host' => '127.0.0.1',
        'port' => 9501,
        'worker_num' => 8,    // worker 进程数目
        'daemonize' => false,  // 守护进程化
        'task_worker_num' => 8, // task 进程数目
        'max_request' => 5,      // 最大请求数:: 实际这个地方用不上
        'task_max_request' => 5000, // task 处理多少个任务退出，防止内存泄漏
                                   // task默认:5000, 值过大不好,会导致CPU过于频繁使用,TIME_WAIT 大量出现
        'log_dir' => '/log/'
    ],
    'redis' => [
        'host' => '127.0.0.1',
        'port' => 6379,
        'password' => '',
        'database' => 0,
    ],
/*    'beanstalk' => [ 暂时没用
        'host' => '127.0.0.1',
        'port' => 11300,
    ],*/
    'mysql' => [
        'type' => 'mysql',
        'host' => '127.0.0.1',
        'port' => 3306,
        'user' => 'root',
        'password' => 'pengyun120',
        'database' => 'webim',
        'charset' => 'utf-8',
        'prefix' => 'sp_' // 表前缀
    ],
];