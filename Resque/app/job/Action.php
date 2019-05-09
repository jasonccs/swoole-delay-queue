<?php
namespace app\job;


/**
 * Class Action
 * @package app\job
 * @author twomiao
 */
class Action
{
    const CANCELLED = -1; // 自动取消订单

    const SEND_EMAIL = 1; // 定时发送邮件
}