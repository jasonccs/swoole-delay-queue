<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/5 0005
 * Time: 下午 15:31
 */

namespace Serve\Interfaces;

interface LoggerInterface
{
    // SeasLog::debug('this is a {userName} debug', array('{userName}' => 'neeke'));
    public static function debug(string $message, array $data);

    //  SeasLog::info('this is a info log',array(),'logger_info_test');
    public static function info(string $message, $data = array());

    // SeasLog::alert('yes this is a {messageName}', array('{messageName}' => 'alertMSG'));
    public static function alert(string $message, array $data);

    // SeasLog::error('a error log');
    public static function error(string $message);

    // SeasLog::notice('this is a notice log');
    public static function notice(string $message);
}