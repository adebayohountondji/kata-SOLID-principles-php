<?php

namespace App\Orders;

class OrderAlreadyExistsError extends \Exception
{
    public function __construct(string $id)
    {
        parent::__construct("Order with id {$id} already exists.");
    }
}