<?php

namespace App\Orders;

class Order
{
    const DEFAULT_VAT_RATE = 0.2;

    public function __construct(
        private readonly int   $id,
        /** @var $items OrderItemData[] */
        private array          $items,
        private float          $sub_total,
        private readonly float $vat_rate,
        private float          $total,
    )
    {
    }

    public function addItem(OrderItemData $data): void
    {
        foreach ($this->items as $index => $item) {
            if ($item->name === $data->name) {
                $this->items[$index] = $data;
                $this->calculate();
                return;
            }
        }

        $this->items[] = $data;
        $this->calculate();
    }

    /** @throws ItemNotFoundError */
    public function deleteItem(string $name): void
    {
        foreach ($this->items as $index => $item) {
            if ($item->name === $name) {
                unset($this->items[$index]);
                $this->calculate();
                return;
            }
        }

        throw new ItemNotFoundError($name);
    }

    private function calculate(): void
    {
        $this->sub_total = 0;

        foreach ($this->items as $item) {
            $this->sub_total += round($item->price * $item->quantity, 2);
        }

        $this->total = round(
            $this->sub_total + ($this->sub_total * $this->vat_rate)
        );
    }

    public function toData(): OrderData
    {
        return new OrderData(
            id: $this->id,
            items: $this->items,
            sub_total: $this->sub_total,
            vat_rate: $this->vat_rate,
            total: $this->total,
        );
    }

    public static function fromData(OrderData $data): Order
    {
        return new Order(...get_object_vars($data));
    }

    public static function create(CreateOrderData $data): Order
    {
        return new Order(
            id: $data->id,
            items: [],
            sub_total: 0,
            vat_rate: $data->vat_rate,
            total: 0,
        );
    }
}