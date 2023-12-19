<?php

namespace App\Domain;

readonly class AddItemToOrderData
{
    public function __construct(
        public OrderData     $order,
        public OrderItemData $item,
    )
    {
    }
}