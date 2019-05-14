<?php

namespace Serve\Exception;

/**
 * Class JobException
 * @package Serve\Exception
 * @version v1.0.1
 * @author twomiao
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