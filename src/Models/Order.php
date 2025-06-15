<?php

namespace App\Models;

use PDO;

class Order {
    public $id;
    public $customer_name;
    public $customer_email;
    public $customer_phone;
    public $shipping_address;
    public $shipping_city;
    public $shipping_country;
    public $total_amount;
    public $status;
    public $created_at;

    protected $pdo;

    public function __construct($pdo = null)
    {
        $this->pdo = $pdo;
    }

    public function all()
    {
        $stmt = $this->pdo->query('SELECT * FROM orders ORDER BY created_at DESC');
        return $stmt->fetchAll(PDO::FETCH_CLASS, self::class);
    }

    public function find($id)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM orders WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetchObject(self::class);
    }

    /**
     * Create a new order. Returns the inserted order ID.
     * @param array $data [customer_name, customer_email, customer_phone, shipping_address, shipping_city, shipping_country]
     * @return int Inserted order ID
     * @throws \Exception
     */
    public function create(array $data)
    {
        $fields = ['customer_name', 'customer_email', 'customer_phone', 'shipping_address', 'shipping_city', 'shipping_country'];
        foreach ($fields as $field) {
            if (empty($data[$field])) {
                throw new \Exception('Missing required field: ' . $field);
            }
        }
        $total = isset($data['total_amount']) ? $data['total_amount'] : 0.00;
        $stmt = $this->pdo->prepare("INSERT INTO orders (customer_name, customer_email, customer_phone, shipping_address, shipping_city, shipping_country, total_amount, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['customer_name'],
            $data['customer_email'],
            $data['customer_phone'],
            $data['shipping_address'],
            $data['shipping_city'],
            $data['shipping_country'],
            $total,
            isset($data['created_at']) ? $data['created_at'] : date('Y-m-d H:i:s')
        ]);
        return (int)$this->pdo->lastInsertId();
    }
}
