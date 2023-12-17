<?php

namespace App\Orders;

readonly class NewOrderData
{
    public function __construct(
        public int   $id,
        public float $vat_rate,
    )
    {
    }
}