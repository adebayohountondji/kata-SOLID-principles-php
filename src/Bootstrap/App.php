<?php

namespace App\Bootstrap;

use App\Domain\Orders\Orders;
use App\Services\Mysql\Config;
use App\Services\Mysql\Mysql;
use App\Services\Mysql\OrdersStorageImpl;

class App
{
    private Mysql $mysql;

    public function __construct(
        Config $mysqlConfig
    )
    {
        $this->mysql = new Mysql($mysqlConfig);
    }

    public function orders(): Orders
    {
        static $orders = null;

        if ($orders === null) {
            $orders = new Orders(
                ordersStorage: new OrdersStorageImpl($this->mysql)
            );
        }

        return $orders;
    }

    public static function createFromEnvVars(): App
    {
        return new App(
            mysqlConfig: Config::createFromEnvVars(),
        );
    }
}