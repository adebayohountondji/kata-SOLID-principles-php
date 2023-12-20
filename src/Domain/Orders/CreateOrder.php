<?php

namespace App\Domain\Orders;

readonly class CreateOrder
{
    public function __construct(
        private OrdersStorage $ordersStorage
    )
    {
    }

    /** @throws OrderAlreadyExistsError */
    public function execute(CreateOrderData $data): void
    {
        if ($this->ordersStorage->has($data->id)) {
            throw new OrderAlreadyExistsError($data->id);
        }

        $order = Order::create($data);

        $this->ordersStorage->save($order->toData());
    }
}