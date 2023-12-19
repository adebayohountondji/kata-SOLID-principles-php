<?php

namespace App\Domain;

readonly class OrderData
{
    public function __construct(
        public int   $id,
        /** @var $items OrderItemData[] */
        public array $items,
        public float $sub_total,
        public float $vat_rate,
        public float $total
    )
    {
    }

    public function __toString(): string
    {
        $vatPercent = $this->vat_rate * 100;
        $items = "";

        foreach ($this->items as $item) {
            $items .= <<<EOD
            $item->name, $item->quantity x {$item->price}€
            EOD;
        }

        if (empty($items)) {
            $items = "Order is empty";
        }

        return <<<EOD
        Order #$this->id
        -----------------------
        $items
        -----------------------
        Sub Total: {$this->sub_total}€
        VAT: {$vatPercent}%
        Total: {$this->total}€
        EOD;
    }
}