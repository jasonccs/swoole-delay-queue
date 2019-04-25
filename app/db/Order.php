<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/30 0030
 * Time: 下午 17:20
 */

namespace app\db;

class Order
{
    const UNPAID_STATUS = -1;
    const CANCEL_STATUS = 0;

    private $table = "order";

    private $order = [];

    private $pdo = null;


    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * 取消这笔订单
     */
    public function cancelled(): bool
    {
        $order = $this->order;
        if ($order) {
            if ($order['orderStats'] == Order::UNPAID_STATUS) {
                if ($this->pdo) {
                    $this->pdo->update($this->table,
                        [
                            "{$this->table}.orderStats" => Order::CANCEL_STATUS,
                            "{$this->table}.updated_at" => date('Y-m-d H:i:s')
                        ],
                        ["id" => $order['id']]
                    );
                    return true;
                }
            }
        }
        return false;
    }

    public function getOrder($orderSn)
    {
        $this->order = $this->pdo->get($this->table, ['id', 'orderStats', 'orderSn', 'create_at'],
            [
                "{$this->table}.orderSn" => $orderSn,
                "{$this->table}.orderSn" => $orderSn,
            ]
        );
        return $this;
    }
}
