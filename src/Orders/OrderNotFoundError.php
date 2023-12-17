<?php

namespace App\Orders;

class OrderNotFoundError extends \Exception
{
    public function __construct(int $id)
    {
        parent::__construct("Order with id {$id} not found.");
    }
}