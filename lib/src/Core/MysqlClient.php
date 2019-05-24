<?php
namespace Serve\Core;

use Medoo\Medoo;

/**
 * Class MysqlClient
 * @package Serve\Core
 * @author twomiao:<995200452@qq.com>
 */
class MysqlClient
{
    private static $mysql = null;

    private function __construct()
    {
    }
    private function __clone()
    {
        // TODO: Implement __clone() method.
    }

    public static function makeInstance(): ?Medoo
    {
        if (empty(self::$mysql))
        {
            self::$mysql = new Medoo([
                'database_type' => Env::get('mysql.type'),
                'database_name' => Env::get('mysql.database'),
                'server'        => Env::get('mysql.host'),
                'username'      => Env::get('mysql.user'),
                'password'      => Env::get('mysql.password'),
                'charset'       => Env::get('mysql.charset'),
                'port'          => Env::get('mysql.port'),
                'prefix'        => Env::get('mysql.prefix'),
                'option' => [
                    \PDO::ATTR_CASE => \PDO::CASE_NATURAL
                ]
            ]);
        }
        return  self::$mysql;
    }
}