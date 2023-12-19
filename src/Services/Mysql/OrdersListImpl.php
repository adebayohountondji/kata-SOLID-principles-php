<?php

namespace App\Services\Mysql;

use App\Domain\OrderData;
use App\Domain\OrderItemData;
use App\Domain\OrderNotFoundError;
use App\Domain\OrdersList;

class OrdersListImpl implements OrdersList
{
    private Mysql $mysql;

    public function __construct(Mysql $mysql)
    {
        $this->mysql = $mysql;
    }

    public function has(string $id): bool
    {
        $result = $this->mysql->fetch(
            new Query(sql: "SELECT COUNT(*) as total FROM orders WHERE id = ?", params: [$id])
        );
        return $result["total"] > 0;
    }

    public function save(OrderData $data): void
    {
        $transaction = $this->mysql->beginTransaction();

        $transaction->add(
            new Query(
                sql: <<<EOD
                    INSERT INTO orders 
                    (id, sub_total, vat_rate, total)
                    VALUES 
                    (:id, :sub_total, :vat_rate, :total)
                    ON DUPLICATE KEY UPDATE 
                    sub_total = :sub_total, vat_rate = :vat_rate, total = :total
                    ;
                    EOD,
                values: [
                    ":id" => $data->id,
                    ":sub_total" => $data->sub_total,
                    ":vat_rate" => $data->vat_rate,
                    ":total" => $data->total,
                ]
            )
        );

        $transaction->add(
            new Query(sql: "DELETE FROM items WHERE order_id = ?", params: [$data->id])
        );

        foreach ($data->items as $item) {
            $transaction->add(
                new Query(
                    sql: <<<EOD
                        INSERT INTO items 
                        (name, price, quantity, order_id)
                        VALUES
                        (?, ?, ?, ?)
                        ;
                        EOD,
                    params: [
                        $item->name,
                        $item->price,
                        $item->quantity,
                        $data->id,
                    ]
                )
            );
        }

        try {
            $transaction->run();
        } catch (\PDOException $e) {
            throw $e;
        }
    }

    /** @throws OrderNotFoundError */
    public function findById(string $id): OrderData
    {
        $data = $this->mysql->fetch(
            new Query(sql: "SELECT id, sub_total, vat_rate, total FROM orders WHERE id = ?", params: [$id])
        );

        if ($data === false) {
            throw new OrderNotFoundError($id);
        }

        $mysqlItems = $this->mysql->fetchAll(
            new Query(sql: "SELECT name, price, quantity FROM items WHERE order_id = ?", params: [$id])
        );

        foreach ($mysqlItems as $item) {
            $data['items'][] = new OrderItemData(...$item);
        }

        if (!isset($data['items'])) {
            $data['items'] = [];
        }

        return new OrderData(...$data);
    }
}