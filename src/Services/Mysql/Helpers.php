<?php

namespace App\Services\Mysql;

class Helpers
{
    public static function executeQuery(Query $query, \PDO $pdo): \PDOStatement
    {
        $stmt = $pdo->prepare($query->sql);

        if ($query->values !== null) {
            foreach ($query->values as $key => $value) {
                $stmt->bindValue($key, $value);
            }
        }

        $stmt->execute($query->params);

        return $stmt;
    }
}