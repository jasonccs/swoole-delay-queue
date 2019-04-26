<?php

namespace Serve\Core;

class Env
{
    /**
     * @var null
     * 获取配置文件参数数据
     */
    private static $config = null;

    public static function get(string $name): ?string
    {
        $data = self::$config ?? null;
        // 有数据,进行解析
        if ($data) {
            list($key, $val) = explode('.', $name);
            return $data[$key][$val] ?? null;
        }
        return $data;
    }

    //public static function load(): ?array
    public static function load()
    {
        $config = APP_PATH . DS . 'config' . DS . 'Serve.ini';
        require_once APP_PATH . DS . 'functions' . DS . 'functions.php';
        self::$config = parse_ini_file($config, true);
       // return self::$config;
    }
}
