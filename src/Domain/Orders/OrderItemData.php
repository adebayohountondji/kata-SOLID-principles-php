<?php

namespace App\Domain\Orders;

readonly class OrderItemData
{
    public function __construct(
        public string $name,
        public float  $price,
        public int    $quantity,
    )
    {
    }
}