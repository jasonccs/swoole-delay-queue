<?php
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
            if ($order['order_stats'] == Order::UNPAID_STATUS) {
                if ($this->pdo) {
                    $this->pdo->update($this->table,
                        [
                            "{$this->table}.order_stats" => Order::CANCEL_STATUS,
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
        $this->order = $this->pdo->get($this->table, ['id', 'order_stats', 'order_sn', 'create_at'],
            [
                "{$this->table}.order_sn" => $orderSn,
                "{$this->table}.order_sn" => $orderSn,
            ]
        );
        return $this;
    }
}
