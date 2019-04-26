<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/4 0004
 * Time: 下午 22:58
 */

namespace Serve\Exception;


class BaseException extends \Exception
{
    public $code = 0;
    public $message = '';

    public function __construct(array $e = [])
    {
        if (array_key_exists('errorCode', $e))
        {
            $this->code = $e['errorCode'];
        }

        if (array_key_exists('message', $e))
        {
            $this->message = $e['message'];
        }
    }
}