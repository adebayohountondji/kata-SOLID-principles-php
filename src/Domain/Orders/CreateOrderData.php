<?php

namespace App\Domain\Orders;

readonly class CreateOrderData
{
    public function __construct(
        public int   $id,
        public float $vat_rate,
    )
    {
    }
}