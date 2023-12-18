<?php

namespace App\Orders;

class ItemNotFoundError extends \Exception
{
    public function __construct(string $name)
    {
        parent::__construct("Item with the name '{$name}' not found in the order.");
    }
}