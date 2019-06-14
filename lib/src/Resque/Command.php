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
     * @var mixed|string
     * stop start reload reload::all
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
    private $name = "Serve-Queue";

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
                $running = Control::isRunning();
                // 没有运行
                if (!$running) {
                    Color::println($this->makeLogo(),ColorText::FG_GREEN);
                    Color::println($this->info(), ColorText::FG_GREEN);
                    (new Serve($this->isDaemon))->run();
                }
                // 已经运行
                if ($running) {
                    $masterPid = Helper::getMasterPid();
                    Log::warning("{$this->name} already running, Master PID:{$masterPid}");
                }
                break;
            case 'stop':
                $isRunning = Control::isRunning();
                // 已经运行进行stop
                if ($isRunning) {
                    if (Control::stop()) {
                        Log::info("{$this->name} has stopped");
                    } else {
                        Log::error("{$this->name} Serve failed to stop");
                    }
                    break;
                }
                Log::info("{$this->name} is not running");
                break;
            case 'reload:all':
                // 1. reload worker and task process.
                if (Control::reloadAll()) {
                    Log::info("Smooth restart successful");
                } else {
                    Log::info("{$this->name} is not running");
                }
                break;
            case 'reload':
                // task 是否运行
                if (Control::reloadTask()) {
                    Log::info("Task process successfully restarted smoothly");
                } else {
                    Log::info("{$this->name} is not running");
                }
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
        $serveVersion = $this->getCurrentVersion();
        $now =  date('Y-m-d H:i:s');
        $php = phpversion();
        return "{$now} {$serveVersion}, PHP: {$php}, Swoole: ".SWOOLE_VERSION;
    }

    public function getCurrentVersion()
    {
        return 'Serve-Queue: v1.0.2';
    }

    /**
     * @return string
     * logo
     */
    public function makeLogo():string {
        return '  ___                              ___                            
 / __|  ___   _ _  __ __  ___     / _ \   _  _   ___   _  _   ___ 
 \__ \ / -_) | \'_| \ V / / -_)   | (_) | | || | / -_) | || | / -_)
 |___/ \___| |_|    \_/  \___|    \__\_\  \_,_| \___|  \_,_| \___|
';
    }
}
