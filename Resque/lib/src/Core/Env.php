<?php

namespace Serve\Core;

class Env
{
    /**
     * @var null
     * 获取配置文件参数数据
     */
    private static $config = null;

    /**
     * @param string $name
     * @return null
     * 获取配置文件参数
     */
    public static function get(string $name)
    {
        $config = self::$config ?? null;

        // 有数据,进行解析
        if ($config) {
            // key.name
            if(substr_count( $name, '.') == 1)
            {
                list($key, $val) = explode('.', $name);
                // env('queue')
                if (isset($key) && isset($val))
                {
                    return $config[$key][$val] ?? null;
                }

                if (isset($key))
                {
                    return $config[$key] ?? null;
                }
            }
        }
        return $config;
    }

    /**
     * 加载配置文件queue.php
     */
    public static function load()
    {
        $config = APP_PATH . DS . 'config' . DS . 'main.php';
        require_once APP_PATH . DS . 'functions' . DS . 'functions.php';
        self::$config = require_once $config;
    }
}
