<?php

namespace App\Services\Mysql;

class Mysql
{
    private \PDO $pdo;

    public function __construct(Config $config)
    {
        $this->pdo = new \PDO(
            "mysql:host={$config->host};dbname={$config->dbname};port={$config->port}",
            $config->user,
            $config->pass
        );

        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    public function beginTransaction(): Transaction
    {
        return new Transaction($this->pdo);
    }

    public function fetch(Query $query): array|false
    {
        return Helpers::executeQuery($query, $this->pdo)
            ->fetch(\PDO::FETCH_ASSOC);
    }

    public function fetchAll(Query $query): array
    {
        return Helpers::executeQuery($query, $this->pdo)
            ->fetchAll(\PDO::FETCH_ASSOC);
    }
}