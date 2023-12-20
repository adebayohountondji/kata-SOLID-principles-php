<?php

namespace App\Bootstrap;

use App\Domain\Orders\Orders;
use App\Services\Mysql\Config;
use App\Services\Mysql\Mysql;
use App\Services\Mysql\OrdersStorageImpl;

readonly class App
{
    public function __construct(
        private Config $mysqlConfig
    )
    {
    }

    public function orders(): Orders
    {
        static $orders = null;

        if ($orders === null) {
            $orders = new Orders(
                ordersStorage: new OrdersStorageImpl($this->mysql())
            );
        }

        return $orders;
    }

    private function mysql(): Mysql
    {
        static $mysql = null;

        if ($mysql === null) {
            $mysql = new Mysql(
                config: $this->mysqlConfig
            );
        }

        return $mysql;
    }

    public static function createFromEnvVars(): App
    {
        return new App(
            mysqlConfig: Config::createFromEnvVars(),
        );
    }
}