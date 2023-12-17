<?php

namespace App\Databases;

class Mysql
{
    private \PDO|null $PDO;

    public function __construct(
        string $dbname,
        string $host,
        string $pass,
        int    $port,
        string $user,
    )
    {
        $this->PDO = new \PDO("mysql:host={$host};dbname={$dbname};port={$port}", $user, $pass);
        $this->PDO->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    public function getPDO(): \PDO
    {
        return $this->PDO;
    }

    public function __destruct()
    {
        $this->PDO = null;
    }

    public static function createFromEnvVars(): Mysql
    {
        return new Mysql(
            dbname: $_ENV["MYSQL_DBNAME"],
            host: $_ENV["MYSQL_HOST"],
            pass: $_ENV["MYSQL_PASS"],
            port: $_ENV["MYSQL_PORT"],
            user: $_ENV["MYSQL_USER"],
        );
    }
}