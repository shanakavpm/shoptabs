<?php

namespace App\Models;

use PDO;
use Exception;

abstract class Model
{
    protected PDO $pdo;
    protected string $table;
    protected array $fillable = [];

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getPdo(): PDO
    {
        return $this->pdo;
    }
}
?>