<?php

namespace App\Domain\Orders;

interface OrdersStorage
{
    public function has(string $id): bool;

    public function save(OrderData $data): void;

    /** @throws OrderNotFoundError */
    public function findById(string $id): OrderData;
}