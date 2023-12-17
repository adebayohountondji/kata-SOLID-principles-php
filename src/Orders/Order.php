<?php

namespace App\Orders;

use App\Databases\Mysql;

class Order
{
    const DEFAULT_VAT_RATE = 0.2;

    public function __construct(
        private readonly int   $id,
        /** @var $items ItemData[] */
        private array          $items,
        private float          $sub_total,
        private readonly float $vat_rate,
        private float          $total,
    )
    {
    }

    public function addItem(ItemData $data): void
    {
        foreach ($this->items as $index => $item) {
            if ($item->name === $data->name) {
                $this->items[$index] = $data;
                $this->calculate();
                return;
            }
        }

        $this->items[] = $data;
        $this->calculate();
    }

    /** @throws ItemNotFoundInOrderError */
    public function deleteItem(string $name): void
    {
        foreach ($this->items as $index => $item) {
            if ($item->name === $name) {
                unset($this->items[$index]);
                $this->calculate();
                return;
            }
        }

        throw new ItemNotFoundInOrderError($name);
    }

    private function calculate(): void
    {
        $this->sub_total = 0;

        foreach ($this->items as $item) {
            $this->sub_total += round($item->price * $item->quantity, 2);
        }

        $this->total = round(
            $this->sub_total + ($this->sub_total * $this->vat_rate)
        );

        self::save($this);
    }

    public function __toString(): string
    {
        $vatPercent = $this->vat_rate * 100;
        $items = "";

        foreach ($this->items as $item) {
            $items .= <<<EOD
            $item->name, $item->quantity x {$item->price}€
            EOD;
        }

        if (empty($items)) {
            $items = "Order is empty";
        }

        return <<<EOD
        Order #$this->id
        -----------------------
        $items
        -----------------------
        Sub Total: {$this->sub_total}€
        VAT: {$vatPercent}%
        Total: {$this->total}€
        EOD;
    }

    /** @throws OrderAlreadyExistsError */
    public static function new(NewOrderData $data): void
    {
        if (self::orderExists($data->id)) {
            throw new OrderAlreadyExistsError($data->id);
        }

        $order = new Order(
            id: $data->id,
            items: [],
            sub_total: 0,
            vat_rate: $data->vat_rate,
            total: 0,
        );

        self::save($order);
    }

    private static function orderExists(string $id): bool
    {
        $pdo = Mysql::createFromEnvVars()
            ->getPDO();

        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM orders WHERE id = :id");
        $stmt->bindValue(":id", $id);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $result["total"] > 0;
    }

    private static function save(Order $order): void
    {
        $pdo = Mysql::createFromEnvVars()
            ->getPDO();

        try {
            $pdo->beginTransaction();

            $saveOrderStmt = $pdo->prepare(
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

            $saveOrderStmt->bindValue(":id", $order->id);
            $saveOrderStmt->bindValue(":sub_total", $order->sub_total);
            $saveOrderStmt->bindValue(":vat_rate", $order->vat_rate);
            $saveOrderStmt->bindValue(":total", $order->total);
            $saveOrderStmt->execute();

            $deleteItemsStmt = $pdo->prepare("DELETE FROM items WHERE order_id = :order_id");
            $deleteItemsStmt->bindValue(":order_id", $order->id);
            $deleteItemsStmt->execute();

            foreach ($order->items as $item) {
                $saveItemStmt = $pdo->prepare(
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
                $saveItemStmt->bindValue(":order_id", $order->id);
                $saveItemStmt->execute();
            }

            $pdo->commit();

        } catch (\PDOException $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    /** @throws OrderNotFoundError */
    public static function findById(string $id): Order
    {
        $pdo = Mysql::createFromEnvVars()
            ->getPDO();

        $orderStmt = $pdo->prepare("SELECT id, sub_total, vat_rate, total FROM orders WHERE id = :id");
        $orderStmt->bindValue(":id", $id);
        $orderStmt->execute();
        $data = $orderStmt->fetch(\PDO::FETCH_ASSOC);

        if ($data === false) {
            throw new OrderNotFoundError($id);
        }

        $itemsStmt = $pdo->prepare("SELECT name, price, quantity FROM items WHERE order_id = :order_id");
        $itemsStmt->bindValue(":order_id", $id);
        $itemsStmt->execute();

        foreach ($itemsStmt->fetchAll(\PDO::FETCH_ASSOC) as $item) {
            $data['items'][] = new ItemData(...$item);
        }

        if (!isset($data['items'])) {
            $data['items'] = [];
        }

        return new Order(...$data);
    }
}