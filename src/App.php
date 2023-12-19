<?php

namespace App;

use App\Domain\OrdersList;
use App\Services\Mysql\Mysql;
use App\Services\Mysql\Config;
use App\Services\Mysql\OrdersListImpl;

class App
{
    private Mysql $mysql;

    public function __construct(
        Config $mysqlConfig
    )
    {
        $this->mysql = new Mysql($mysqlConfig);
    }

    public function orderList(): OrdersList
    {
        return new OrdersListImpl($this->mysql);
    }

    public static function createFromEnvVars(): App
    {
        return new App(
            mysqlConfig: Config::createFromEnvVars(),
        );
    }
}