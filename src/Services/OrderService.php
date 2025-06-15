<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Payment;
use PDO;

class OrderService
{
    protected $order;
    protected $orderItem;
    protected $product;
    protected $payment;
    protected $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->order = new Order($pdo);
        $this->orderItem = new OrderItem($pdo);
        $this->product = new Product($pdo);
        $this->payment = new Payment($pdo);
    }

    public function createOrderWithItemsAndPayment(array $orderData, array $cart)
    {
        $this->pdo->beginTransaction();
        try {
            // Calculate total amount
            $total = 0.00;
            foreach ($cart as $productId => $qty) {
                $product = $this->product->find($productId);
                if (!$product) throw new \Exception('Invalid product in cart.');
                $total += $product->price * (int)$qty;
            }
            $orderData['total_amount'] = $total;
            $orderId = $this->order->create($orderData);
            foreach ($cart as $productId => $qty) {
                $product = $this->product->find($productId);
                $this->orderItem->create($orderId, [
                    'product_id' => $productId,
                    'quantity' => (int)$qty,
                    'price' => $product->price
                ]);
            }
            $this->payment->create($orderId, 'pending', null, null);
            $this->pdo->commit();
            return $orderId;
        } catch (\Exception $e) {
            if ($this->pdo->inTransaction()) $this->pdo->rollBack();
            throw $e;
        }
    }
}
