<?php

namespace App\Orders;

readonly class AddItemToOrderData
{
    public function __construct(
        public OrderData     $order,
        public OrderItemData $item,
    )
    {
    }
}