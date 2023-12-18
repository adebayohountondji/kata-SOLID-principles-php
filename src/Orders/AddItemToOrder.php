<?php

namespace App\Orders;

readonly class AddItemToOrder
{
    public function __construct(
        private OrdersList $ordersList
    )
    {
    }

    public function execute(AddItemToOrderData $data): void
    {
        $order = Order::fromData($data->order);
        $order->addItem($data->item);

        $this->ordersList->save($order->toData());
    }
}