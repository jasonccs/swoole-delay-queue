<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/6 0006
 * Time: 下午 22:28
 */

namespace Serve\Core;


use Serve\Interfaces\ClientInterface;

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