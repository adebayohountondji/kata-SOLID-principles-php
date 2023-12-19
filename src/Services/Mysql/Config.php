<?php

namespace App\Services\Mysql;

readonly class Config
{
    public function __construct(
        public string $dbname,
        public string $host,
        public string $pass,
        public int    $port,
        public string $user,
    )
    {
    }

    public static function createFromEnvVars(): Config
    {
        return new Config(
            dbname: $_ENV["MYSQL_DBNAME"],
            host: $_ENV["MYSQL_HOST"],
            pass: $_ENV["MYSQL_PASS"],
            port: $_ENV["MYSQL_PORT"],
            user: $_ENV["MYSQL_USER"],
        );
    }
}