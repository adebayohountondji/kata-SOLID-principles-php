<?php

namespace App\Services\Mysql;

readonly class Query
{
    public function __construct(
        public string $sql,
        public ?array $params = null,
        public ?array $values = null,
    )
    {
    }
}