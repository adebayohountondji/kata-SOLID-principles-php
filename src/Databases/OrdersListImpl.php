<?php

namespace App\Databases;

use App\Orders\OrderItemData;
use App\Orders\OrderData;
use App\Orders\OrderNotFoundError;
use App\Orders\OrdersList;

class OrdersListImpl implements OrdersList
{
    private MysqlConnection $connection;

    public function __construct(MysqlConnection $connection)
    {
        $this->connection = $connection;
    }

    public function has(string $id): bool
    {
        $stmt = $this->connection->getPdo()->prepare("SELECT COUNT(*) as total FROM orders WHERE id = :id");
        $stmt->bindValue(":id", $id);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $result["total"] > 0;
    }

    public function save(OrderData $data): void
    {
        try {
            $this->connection->getPdo()->beginTransaction();

            $saveOrderStmt = $this->connection->getPdo()->prepare(
                <<<EOD
                INSERT INTO orders 
                    (id, sub_total, vat_rate, total)
                VALUES 
                    (:id, :sub_total, :vat_rate, :total)
                ON DUPLICATE KEY UPDATE 
                    sub_total = :sub_total, vat_rate = :vat_rate, total = :total
                ;
                EOD
            );

            $saveOrderStmt->bindValue(":id", $data->id);
            $saveOrderStmt->bindValue(":sub_total", $data->sub_total);
            $saveOrderStmt->bindValue(":vat_rate", $data->vat_rate);
            $saveOrderStmt->bindValue(":total", $data->total);
            $saveOrderStmt->execute();

            $deleteItemsStmt = $this->connection->getPdo()->prepare("DELETE FROM items WHERE order_id = :order_id");
            $deleteItemsStmt->bindValue(":order_id", $data->id);
            $deleteItemsStmt->execute();

            foreach ($data->items as $item) {
                $saveItemStmt = $this->connection->getPdo()->prepare(
                    <<<EOD
                    INSERT INTO items 
                    (name, price, quantity, order_id)
                    VALUES
                        (:name, :price, :quantity, :order_id)
                    ;
                    EOD
                );

                $saveItemStmt->bindValue(":name", $item->name);
                $saveItemStmt->bindValue(":price", $item->price);
                $saveItemStmt->bindValue(":quantity", $item->quantity);
                $saveItemStmt->bindValue(":order_id", $data->id);
                $saveItemStmt->execute();
            }

            $this->connection->getPdo()->commit();

        } catch (\PDOException $e) {
            $this->connection->getPdo()->rollBack();
            throw $e;
        }
    }

    /** @throws OrderNotFoundError */
    public function findById(string $id): OrderData
    {
        $orderStmt = $this->connection->getPdo()
            ->prepare("SELECT id, sub_total, vat_rate, total FROM orders WHERE id = :id");

        $orderStmt->bindValue(":id", $id);
        $orderStmt->execute();
        $data = $orderStmt->fetch(\PDO::FETCH_ASSOC);

        if ($data === false) {
            throw new OrderNotFoundError($id);
        }

        $itemsStmt = $this->connection->getPdo()
            ->prepare("SELECT name, price, quantity FROM items WHERE order_id = :order_id");

        $itemsStmt->bindValue(":order_id", $id);
        $itemsStmt->execute();

        foreach ($itemsStmt->fetchAll(\PDO::FETCH_ASSOC) as $item) {
            $data['items'][] = new OrderItemData(...$item);
        }

        if (!isset($data['items'])) {
            $data['items'] = [];
        }

        return new OrderData(...$data);
    }
}