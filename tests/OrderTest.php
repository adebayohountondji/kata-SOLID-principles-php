<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class OrderTest extends TestCase
{
    public function test(): void
    {
        $order = \App\Domain\Order::create(
            new \App\Domain\CreateOrderData(
                id: 1,
                vat_rate: \App\Domain\Order::DEFAULT_VAT_RATE
            )
        );

        $order->addItem(
            new \App\Domain\OrderItemData(
                name: "Book",
                price: 100,
                quantity: 2
            )
        );

        $this->assertEquals(240, $order->toData()->total);
    }
}