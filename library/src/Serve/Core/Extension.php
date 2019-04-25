<?php

namespace Serve\Core;

class Extension
{
    private $versionCompare = [
        'swoole' => ['4.3.1', '>='],
//        'seaslog' => ['2.0.2', '>='],
    ];

    /**
     * @var string
     * 获取扩展版本号
     */
    private $versionModule = '0.0';

    /**
     * 判断扩展是否已安装
     */
    private function check()
    {
        foreach ($this->versionCompare as $name => $version) {
            if (!extension_loaded($name)) {
                $now = \date('Y-m-d H:i:s');
                exit("[ Boot failure ] $now The {$name} extension is not installed.\n");
            }

            $this->getVersion($name);
            list ($newVersion, $operator) = [$version[0], $version[1]];

            if (!version_compare($this->versionModule, $newVersion, $operator)) {
                exit("[ Boot failure ] The {$name} extension is version {$this->versionModule} and must be {$operator} {$newVersion}\n");
            }
        }
    }

    /**
     * @param string $name
     * @return string|null
     * 通过扩展名称获取扩展版本
     */
    private function getVersion(string $name): void
    {
        switch ($name) {
            case 'swoole':
                $this->versionModule = \swoole_version();
                break;
//            case 'seaslog':
//                $this->versionModule = \seaslog_get_version();
//                break;
        }
    }

    public static function checkInstalled(): void
    {
        (new self())->check();
    }
}