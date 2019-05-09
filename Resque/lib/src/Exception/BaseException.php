<?php

namespace Serve\Exception;

/**
 * Class BaseException
 * @package Serve\Exception
 * @version v1.0.1
 * @author twomiao
 */
class BaseException extends \Exception
{
    public $code = 0;
    public $message = '';

    public function __construct(array $e = [
        'code' => '',
        'message' => ''
    ])
    {
        if (array_key_exists('code', $e)) {
            $this->code = $e['code'];
        }

        if (array_key_exists('message', $e)) {
            $this->message = $e['message'];
        }
    }
}