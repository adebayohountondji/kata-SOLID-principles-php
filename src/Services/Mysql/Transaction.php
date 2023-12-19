<?php

namespace App\Services\Mysql;

readonly class Transaction
{
    public function __construct(
        private \PDO $pdo
    )
    {
        $this->pdo->beginTransaction();
    }

    public function add(Query $query): void
    {
        Helpers::executeQuery($query, $this->pdo);
    }

    public function run(): void
    {
        try {
            $this->pdo->commit();
        } catch (\PDOException $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}