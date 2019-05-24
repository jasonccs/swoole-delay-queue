<?php

namespace Serve\Resque;

use Serve\Colors\Color;
use Serve\Colors\ColorText;
use Serve\Core\Env;
use Serve\Core\Helper;
use Serve\Core\Log;

/**
 * Class Command
 * @package Serve\Core
 * @author twomiao:<995200452@qq.com>
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
        start -d/Start Daemon Serve services.
        stop/Stop Serve services.
        reload/Smoothing restart task process.
        reload:all/Smooth restart of all processes.';

    /**
     * @var string
     * 服务名称
     */
    private $name = "Serve";

    private $isDaemon = false;

    public function __construct($cmd)
    {
        $this->initialize();
        //register_argc_argv
        if (ini_get('register_argc_argv')) {
            $this->cmd = $cmd[1] ?? '';
            $this->isDaemon = $cmd[2] ?? '';
        } else {
            Log::info("Turn on register_argc_argv in the php.ini file.");
        }
    }

    public function execute()
    {
        switch (strtolower($this->cmd)) {
            case 'start':
                $running = ControlPanel::isRunning();
                // 没有运行
                if (!$running) {
                    Color::println($this->makeLog(),ColorText::FG_LIGHT_CYAN);
                    Color::println($this->info(), ColorText::FG_WHITE);
                    (new Serve($this->isDaemon))->run();
                }
                // 已经运行
                if ($running) {
                    $masterPid = Helper::getMasterPid();
                    Log::info("Service already running, main process number:{$masterPid}");
                }//
                break;
            case 'stop':
                $isRunning = ControlPanel::isRunning();
                // 已经运行进行stop
                if ($isRunning) {
                    $stopped = ControlPanel::stop();
                    if ($stopped) {
                        Log::info("Service has stopped");
                        break;
                    }
                    Log::error("Service Serve failed to stop");
                    break;
                }
                Log::info("Service is not running");
                break;
            case 'reload:all':
                // 1. reload worker and task process.
                $reload = ControlPanel::reloadAll();
                // reload 成功
                if ($reload) {
                    Log::info("Smooth restart successful");
                    break;
                }
                Log::info("Service is not running");
                break;
            case 'reload':
                $task = ControlPanel::reloadTask();
                // task 是否运行
                if ($task) {
                    Log::info("Task process successfully restarted smoothly");
                    break;
                }
                Log::info("{$this->name} is not running");
                break;
            default:
                exit($this->help . PHP_EOL);
                break;
        }
    }

    public function initialize()
    {
        date_default_timezone_set('PRC');
        Env::load();
        Log::init();
    }

    /**
     * @return string
     * 版本信息
     */
    public function info() {
        return date('Y-m-d H:i:s')." PHP: ".phpversion().", Swoole: ".SWOOLE_VERSION;
    }

    /**
     * @return string
     * logo
     */
    public function makeLog():string {
        return '  ___                              ___                            
 / __|  ___   _ _  __ __  ___     / _ \   _  _   ___   _  _   ___ 
 \__ \ / -_) | \'_| \ V / / -_)   | (_) | | || | / -_) | || | / -_)
 |___/ \___| |_|    \_/  \___|    \__\_\  \_,_| \___|  \_,_| \___|
';
    }
}
