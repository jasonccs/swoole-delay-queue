<?php
namespace Serve\Core;

class Env
{
    /**
     * @var null
     * 读取出来的全部配置参数
     */
    private static $mapData = null;

    public static function get(string $name): ?string // swoole.host
    {
        $param = self::$mapData ?: self::$mapData;
        if ($param) {
            list($outer, $inner) = explode('.', $name);
            return $param[$outer][$inner] ?? null;
        }
        return null;
    }

    public static function load(): ?array
    {
        require_once PathManager::getFunctionsPath();
        self::$mapData = parse_ini_file(PathManager::getConfigPath(), true);
        return self::$mapData;
    }
}