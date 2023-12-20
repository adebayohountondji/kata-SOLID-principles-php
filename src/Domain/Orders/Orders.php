<?php

namespace App\Domain\Orders;

readonly class Orders
{
    public function __construct(
        private OrdersStorage $ordersStorage
    )
    {
    }

    /** @throws OrderAlreadyExistsError */
    public function createNew(CreateOrderData $data): void
    {
        (new CreateOrder($this->ordersStorage))->execute($data);
    }

    /** @throws OrderNotFoundError */
    public function findById(int $id): OrderData
    {
        return (new FindOrderById($this->ordersStorage))->execute($id);
    }

    public function addItemToOrder(AddItemToOrderData $data): void
    {
        (new AddItemToOrder($this->ordersStorage))->execute($data);
    }
}