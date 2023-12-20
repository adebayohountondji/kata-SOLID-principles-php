<?php

namespace App\Domain\Orders;

readonly class AddItemToOrder
{
    public function __construct(
        private OrdersStorage $ordersStorage
    )
    {
    }

    /** @throws OrderNotFoundError */
    public function execute(AddItemToOrderData $data): void
    {
        $order = Order::fromData($this->ordersStorage->findById($data->orderId));
        $order->addItem($data->orderItem);

        $this->ordersStorage->save($order->toData());
    }
}