<?php
namespace Serve\Core;

use Serve\Interfaces\IClient;

/**
 * Class ClientFactory
 * @package Serve\Core
 * @author twomiao
 */
class ClientFactory implements IClient
{
    public static function makeClient(string $name)
    {
        switch ($name) {
            case 'delayer':
            case 'redis':
                return (new RedisClient())->make();
                break;
            case 'mysql':
                return (new MysqlClient())->make();
                break;
        }
    }
}