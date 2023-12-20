<?php

namespace App\Domain\Orders;

readonly class FindOrderById
{
    public function __construct(
        private OrdersStorage $ordersStorage
    )
    {
    }

    /** @throws OrderNotFoundError */
    public function execute(string $id): OrderData
    {
        return $this->ordersStorage->findById($id);
    }
}