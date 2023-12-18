<?php

namespace App\Orders;

interface OrdersList
{
    public function has(string $id): bool;

    public function save(OrderData $data): void;

    public function findById(string $id): OrderData;
}