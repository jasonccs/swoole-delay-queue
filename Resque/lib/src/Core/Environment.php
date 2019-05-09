<?php

namespace Serve\Core;

use Serve\Colors\Color;
use Serve\Colors\ColorText;

class Environment
{
    private $versionCompare = [
        'swoole' => ['4.0.0', '>='],
//        'seaslog' => ['2.0.2', '>='],
    ];

    /**
     * @var string
     * 获取扩展版本号
     */
    private $versionNumber = '0.0';

    /**
     * 判断扩展是否已安装
     */
    private function check()
    {
        $now = date('Y-m-d H:i:s');

        // 非Windows 系统正常运行
        if (!$this->isLinux())
        {
            $error = "[ ERROR ] {$now} Only support Linux system.";
            $this->error($error);
        }

        if ($this->oldPhpVersion()) {
            $error = "[ ERROR ] {$now} php version >= at least 7.1.0.";
            $this->error($error);
        }

        foreach ($this->versionCompare as $name => $version) {
            $this->getSoftWareVer($name);
            list ($newVersion, $operator) = [$version[0], $version[1]];

            if (!extension_loaded($name) || !version_compare($this->versionNumber, $newVersion, $operator)) {
                $error = "[ ERROR ] {$now} {$name} version {$operator} at least {$newVersion} or extension is not installed.";
                $this->error($error);
            }
        }
    }

    private function isLinux(): bool
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
            return true;
        }
        return false;
    }


    /**
     * @return bool
     * PHP 版本检测
     */
    private function oldPhpVersion(): bool
    {
        if (version_compare(phpversion(), '7.1.0', '<')) {
            return true;
        }
        return false;
    }

    private function error($error): void
    {
        Color::println($error, ColorText::RED_FONT);
        exit;
    }

    private function getSoftWareVer(string $name): void
    {
        switch ($name) {
            case 'swoole':
                $this->versionNumber = \swoole_version();
                break;
//            case 'seaslog':
//                $this->versionNumber = \seaslog_get_version();
//                break;
        }
    }

    public static function checkOrFailed(): void
    {
        (new self())->check();
    }
}
