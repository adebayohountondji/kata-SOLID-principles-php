<?php

namespace App\Orders;

readonly class ItemData
{
    public function __construct(
        public string $name,
        public float  $price,
        public int    $quantity,
    )
    {
    }
}