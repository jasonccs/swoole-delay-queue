<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/16 0016
 * Time: 下午 17:54
 */

namespace Serve\Exception;

class JobClassNotFoundException extends BaseException
{
    public $code = 404;
    public $message = 'No class Job was found.';
}