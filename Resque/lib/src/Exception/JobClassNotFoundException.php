<?php
namespace Serve\Exception;

/**
 * Class JobClassNotFoundException
 * @package Serve\Exception
 * @author twomiao
 * @version v1.0.1
 */
class JobClassNotFoundException extends BaseException
{
    public $code = 404;
    public $message = 'No class Job was found.';
}