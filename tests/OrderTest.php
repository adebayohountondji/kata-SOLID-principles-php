<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class OrderTest extends TestCase
{
    public function test(): void
    {
        $order = \App\Orders\Order::create(
            new \App\Orders\CreateOrderData(
                id: 1,
                vat_rate: \App\Orders\Order::DEFAULT_VAT_RATE
            )
        );

        $order->addItem(
            new \App\Orders\OrderItemData(
                name: "Book",
                price: 100,
                quantity: 2
            )
        );

        $this->assertEquals(240, $order->toData()->total);
    }
}