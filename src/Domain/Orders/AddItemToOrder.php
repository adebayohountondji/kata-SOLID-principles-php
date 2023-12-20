<?php

namespace App\Domain\Orders;

readonly class AddItemToOrder
{
    public function __construct(
        private OrdersStorage $ordersStorage
    )
    {
    }

    public function execute(AddItemToOrderData $data): void
    {
        $order = Order::fromData($data->order);
        $order->addItem($data->item);

        $this->ordersStorage->save($order->toData());
    }
}