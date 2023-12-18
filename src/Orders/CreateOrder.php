<?php

namespace App\Orders;

readonly class CreateOrder
{
    public function __construct(
        private OrdersList $ordersList
    )
    {
    }

    /** @throws OrderAlreadyExistsError */
    public function execute(CreateOrderData $data): void
    {
        if ($this->ordersList->has($data->id)) {
            throw new OrderAlreadyExistsError($data->id);
        }

        $order = Order::create($data);

        $this->ordersList->save($order->toData());
    }
}