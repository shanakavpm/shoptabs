<?php

namespace App\Models;

use PDO;

class Payment {
    public $id;
    public $order_id;
    public $payment_request_payload;
    public $payment_response_payload;
    public $status;
    public $created_at;

    protected $pdo;

    public function __construct($pdo = null)
    {
        $this->pdo = $pdo;
    }

    public function findByOrder($order_id)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM payments WHERE order_id = ?');
        $stmt->execute([$order_id]);
        return $stmt->fetchObject(self::class);
    }

    public function create($order_id, $status = 'pending', $request_payload = null, $response_payload = null)
    {
        $now = date('Y-m-d H:i:s');
        $stmt = $this->pdo->prepare('INSERT INTO payments (order_id, status, payment_request_payload, payment_response_payload, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([$order_id, $status, $request_payload, $response_payload, $now, $now]);
        return $this->pdo->lastInsertId();
    }

    public function updateStatus($order_id, $status, $request_payload = null, $response_payload = null)
    {
        $stmt = $this->pdo->prepare('UPDATE payments SET status = ?, payment_request_payload = ?, payment_response_payload = ?, updated_at = NOW() WHERE order_id = ?');
        return $stmt->execute([$status, $request_payload, $response_payload, $order_id]);
    }
}

