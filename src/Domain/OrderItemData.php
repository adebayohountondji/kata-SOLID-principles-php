<?php

namespace App\Domain;

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