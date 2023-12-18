<?php

namespace App\Orders;

readonly class FindOrderById
{
    public function __construct(
        private OrdersList $ordersList
    )
    {
    }

    public function execute(string $id): OrderData
    {
        return $this->ordersList->findById($id);
    }
}