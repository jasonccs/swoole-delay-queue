<?php

namespace Serve\Core;

use Serve\Resque\ControlPanel;
use Serve\Resque\Serve;
use Serve\Resque\Server;

/**
 * Class Command
 * @package Serve\Core
 * @author twomiao
 */
class Command
{
    /**
     * @var string
     * stop、start、reload、reload::all
     */
    private $cmd = '';

    private $help = 'Usage: php Serve [options]
        Options:
        start/Start Serve services.
        stop/Stop Serve services.
        reload/Smoothing restart task process.
        reload:all/Smooth restart of all processes.';

    /**
     * @var string
     * 服务名称
     */
    private $name = "Serve";

    public function __construct($cmd)
    {
        $this->initParams();
        //register_argc_argv
        if (ini_get('register_argc_argv')) {
            $this->cmd = $cmd[1] ?? '';
        } else {
            Log::info("Turn on register_argc_argv in the php.ini file.");
        }
    }

    public function execute()
    {
        $cmd = strtolower($this->cmd);
        switch ($cmd) {
            case 'start':
                $running = ControlPanel::isRunning();
                // 没有运行
                if (!$running) {
                    (new Serve())->run();
                }
                // 已经运行
                if ($running) {
                    $masterPid = Process::getMasterPid();
                    Log::info("{$this->name} is running, Master pid is: {$masterPid}.");
                }
                break;
            case 'stop':
                $isRunning = ControlPanel::isRunning();
                // 已经运行进行stop
                if ($isRunning) {
                    $stopped = ControlPanel::stop();
                    if ($stopped) {
                        Log::info("{$this->name} has stopped.");
                        break;
                    }
                    Log::error("{$this->name} 服务停止失败.");
                    break;
                }
                Log::info("{$this->name} is not running.");
                break;
            case 'reload:all':
                // 1. reload worker and task process.
                $reload = ControlPanel::reloadAll();
                // reload 成功
                if ($reload) {
                    Log::info("Reload task and worker succeeded in the process");
                    break;
                }
                Log::info("{$this->name} is not running.");
                break;
            case 'reload':
                $task = ControlPanel::reloadTask();
                // task 是否运行
                if ($task) {
                    Log::info("Reload task succeeded in the process.");
                    break;
                }
                Log::info("{$this->name} is not running.");
                break;
            default:
                exit($this->help . PHP_EOL);
                break;
        }
    }

    /**
     * 初始化参数
     */
    public function initParams()
    {
        date_default_timezone_set('Asia/Shanghai');
        Env::load();
        Log::init();
    }
}
