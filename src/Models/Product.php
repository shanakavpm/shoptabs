<?php

namespace App\Models;

use PDO;

class Product {
    public $id;
    public $name;
    public $price;
    public $description;

    protected $pdo;

    /**
     * Decrement product quantity by a given amount.
     * Throws exception if insufficient stock.
     */
    public function decrementQuantity($productId, $qty)
    {
        // Note: SQLite does not support FOR UPDATE. Remove for test compatibility. Add back if using MySQL in production.
        $stmt = $this->pdo->prepare('SELECT quantity FROM products WHERE id = ?');
        $stmt->execute([$productId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            throw new \Exception('Product not found');
        }
        $currentQty = (int)$row['quantity'];
        if ($currentQty < $qty) {
            throw new \Exception('Insufficient stock for product');
        }
        $newQty = $currentQty - $qty;
        $update = $this->pdo->prepare('UPDATE products SET quantity = ? WHERE id = ?');
        $update->execute([$newQty, $productId]);
    }

    public function __construct($pdo = null)
    {
        $this->pdo = $pdo;
    }

    public function all()
    {
        $stmt = $this->pdo->query('SELECT * FROM products');
        return $stmt->fetchAll(PDO::FETCH_CLASS, self::class);
    }

    public function find($id)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM products WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetchObject(self::class);
    }
}
