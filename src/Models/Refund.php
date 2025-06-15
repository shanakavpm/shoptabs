<?php

namespace App\Models;

use PDO;

class Refund {
    public $id;
    public $order_id;
    public $payment_id;
    public $request_payload;
    public $response_payload;
    public $created_at;

    protected $pdo;

    public function __construct($pdo = null)
    {
        $this->pdo = $pdo;
    }

    public function whereOrder($order_id)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM refunds WHERE order_id = ?');
        $stmt->execute([$order_id]);
        return $stmt->fetchAll(PDO::FETCH_CLASS, self::class);
    }

    // Added for compatibility with OrderController::show
    public function wherePayment($payment_id)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM refunds WHERE payment_id = ?');
        $stmt->execute([$payment_id]);
        return $stmt->fetchAll(PDO::FETCH_CLASS, self::class);
    }
}
