<?php
namespace Serve\Core;

use Medoo\Medoo;

/**
 * Class MysqlClient
 * @package Serve\Core
 * @author twomiao
 */
class MysqlClient
{
    private $mysql = null;

    public function make(): ?Medoo
    {
        $this->mysql = new Medoo([
            'database_type' => Env::get('database.type'),
            'database_name' => Env::get('database.database'),
            'server'        => Env::get('database.host'),
            'username'      => Env::get('database.user'),
            'password'      => Env::get('database.password'),
            'charset'       => Env::get('database.charset'),
            'port'          => Env::get('database.port'),
            'prefix'        => Env::get('database.prefix'),
            'option' => [
                \PDO::ATTR_CASE => \PDO::CASE_NATURAL
            ]
        ]);
        return $this->mysql;
    }
}