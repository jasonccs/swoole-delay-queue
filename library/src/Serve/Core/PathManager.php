<?php

namespace Serve\Core;


class PathManager
{
    const FUNCTIONS_PATH  = '../../app/functions/functions.php';

    const CONFIG_PATH     = '../../../app/config/Serve.ini';

    const JOB_PATH        = '../../app/logic/Job.php';

    /**
     * @return mixed
     * 配置文件加载
     */
    public static function getConfigPath()
    {
        $configPath = dirname(__DIR__) . DS . self::CONFIG_PATH;
        return str_replace("\\", "/", $configPath);
    }

    /**
     * @return string
     * 函数文件加载
     */
    public static function getFunctionsPath()
    {
        defined('DS') or define('DS', DIRECTORY_SEPARATOR);
        return dirname(dirname(__DIR__)) . DS . self::FUNCTIONS_PATH;
    }
}