<?php

namespace App\Domain\Orders;

readonly class AddItemToOrderData
{
    public function __construct(
        public int           $orderId,
        public OrderItemData $orderItem,
    )
    {
    }
}