<?php
namespace Serve\Core;

use Serve\Resque\Serve;
use Serve\Resque\Server;

/**
 * Class Command
 * @package Serve\Core
 * @author twomiao
 */
class Command
{
    private $cmd = '';

    public function __construct($cmd)
    {
        //register_argc_argv
        if (ini_get('register_argc_argv')) {
            $this->cmd = $cmd[1] ?? '';
        } else {
            Log::info("Turn on register_argc_argv in the php.ini file.");
        }
    }

    public function execute()
    {
        Env::load();
        switch (strtolower($this->cmd)) {
            case 'start':
                if (!Server::isRunning()) {
                    Serve::run();
                } else {
                    $masterPid = Server::getMasterPid();
                    Log::info("Resque is running, Master pid is: {$masterPid}.");
                }
                break;
            case 'stop':
                if (Server::isRunning())
                {
                    $stop = ProcessHelper::killBySig(Server::getMasterPid(), SIGTERM);
                    if ($stop) {
                        Log::info("Resque has stopped.");
                    }
                    // return false  -> 没有运行
                    if (!$stop) {
                        Log::info("Resque is not running.");
                    }
                } else {
                    Log::info("Resque is not running.");
                }
                break;
            case 'reload:all':
                // 1. reload worker and task process.
                $reloadOk = Server::reloadAll();
                if ($reloadOk) {
                    Log::info("Task process and worker process reload succeed.");
                }
                if (!$reloadOk) {
                    Log::info("Resque is not running.");
                }
                break;
            case 'reload':
                if (Server::reloadTaskWorker()) {
                    Log::info("Task process reload succeed.");
                } else {
                    Log::info("Resque is not running.");
                }
                break;
            default:
                exit("Usage: php serve start|stop|reload|reload:all" . PHP_EOL);
                break;
        }
    }
}