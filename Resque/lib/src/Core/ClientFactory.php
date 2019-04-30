<?php
namespace Serve\Core;

use Serve\Interfaces\ClientInterface;

/**
 * Class ClientFactory
 * @package Serve\Core
 * @author twomiao
 */
class ClientFactory implements ClientInterface
{
    public static function makeClient(string $clientName)
    {
        switch ($clientName) {
            case 'predis':
            case 'redis':
                return (new RedisClient())->make();
                break;
            case 'mysql':
                return (new MysqlClient())->make();
                break;
        }
    }
}