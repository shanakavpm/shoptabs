<?php

namespace App\Models;

use PDO;

class OrderItem {
    public $id;
    public $order_id;
    public $product_id;
    public $quantity;
    public $price;

    protected $pdo;

    public function __construct($pdo = null)
    {
        $this->pdo = $pdo;
    }

    public function whereOrder($order_id)
    {
        $sql = 'SELECT oi.*, p.name as product_name, p.description as product_description, p.image as product_image, p.price as product_price FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$order_id]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Create a new order item. Returns true on success.
     * @param int $order_id
     * @param array $data [product_id, quantity, price]
     * @return bool
     * @throws \Exception
     */
    public function create($order_id, array $data)
    {
        $fields = ['product_id', 'quantity', 'price'];
        foreach ($fields as $field) {
            if (!isset($data[$field])) {
                throw new \Exception('Missing required field: ' . $field);
            }
        }
        $stmt = $this->pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        return $stmt->execute([
            $order_id,
            $data['product_id'],
            $data['quantity'],
            $data['price']
        ]);
    }
}
