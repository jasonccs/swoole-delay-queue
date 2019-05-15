<?php
namespace app\job;


/**
 * Class Action
 * @package app\job
 * @author twomiao
 */
class Action
{
    const ORDER_CANCELLED = -1; // 自动取消订单

    const MAILER_SEND = 1; // 定时发送邮件
}