<?php

namespace App\Databases;

class MysqlConnection
{
    private \PDO|null $pdo;

    public function __construct(
        string $dbname,
        string $host,
        string $pass,
        int    $port,
        string $user,
    )
    {
        $this->pdo = new \PDO("mysql:host={$host};dbname={$dbname};port={$port}", $user, $pass);
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    public function getPdo(): ?\PDO
    {
        return $this->pdo;
    }

    public function __destruct()
    {
        $this->pdo = null;
    }

    public static function createFromEnvVars(): MysqlConnection
    {
        return new MysqlConnection(
            dbname: $_ENV["MYSQL_DBNAME"],
            host: $_ENV["MYSQL_HOST"],
            pass: $_ENV["MYSQL_PASS"],
            port: $_ENV["MYSQL_PORT"],
            user: $_ENV["MYSQL_USER"],
        );
    }
}