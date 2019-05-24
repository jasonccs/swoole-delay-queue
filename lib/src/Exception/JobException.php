<?php

namespace Serve\Exception;

/**
 * Class JobException
 * @package Serve\Exception
 * @author twomiao:<995200452@qq.com>
 */
class JobException extends \Exception
{
    public $code = -1;
    public $message = 'No class Job was found.';

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