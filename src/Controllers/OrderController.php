<?php

namespace App\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Payment;
use App\Models\Refund;
use App\Services\OrderService;

class OrderController extends Controller
{
    protected $order;
    protected $orderItem;
    protected $payment;
    protected $refund;
    protected $product;
    protected $orderService;

    public function __construct($pdo)
    {
        parent::__construct($pdo);
        $this->order = new Order($pdo);
        $this->orderItem = new OrderItem($pdo);
        $this->payment = new Payment($pdo);
        $this->refund = new Refund($pdo);
        $this->product = new Product($pdo);
        $this->orderService = new OrderService($pdo);
    }

    public function index()
    {
        $orders = $this->order->all();
        return $this->renderView('orders/index', compact('orders'));
    }

    public function show($id)
    {
        $order = $this->order->find($id);
        $order_items = $this->orderItem->whereOrder($id);
        $payment = $this->payment->findByOrder($id);
        $refunds = $payment ? $this->refund->wherePayment($payment->id) : [];
        // Fetch all products and index by id
        $products = [];
        foreach ($this->product->all() as $p) {
            $products[$p->id] = $p;
        }
        return $this->renderView('orders/show', compact('order', 'order_items', 'payment', 'refunds', 'products'));
    }

    /**
     * Handle AJAX order submission from checkout page.
     * Validates input, creates order and items using models, returns JSON response.
     */
    public function storeAjax()
    {
        header('Content-Type: application/json');
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            $this->validateOrderData($data);
            $orderData = [
                'customer_name' => trim($data['customer_name']),
                'customer_email' => trim($data['customer_email']),
                'customer_phone' => trim($data['customer_phone'] ?? ''),
                'shipping_address' => trim($data['customer_street'] ?? ''),
                'shipping_city' => trim($data['customer_city'] ?? ''),
                'shipping_country' => trim($data['customer_country'] ?? ''),
            ];
            $cart = $data['cart'];
            $orderId = $this->orderService->createOrderWithItemsAndPayment($orderData, $cart);
            echo json_encode(['success' => true, 'order_id' => $orderId]);
        } catch (\Exception $e) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit;
    }

    private function validateOrderData($data)
    {
        if (!$data) throw new \Exception('Invalid data');
        if (
            empty($data['customer_name']) ||
            empty($data['customer_email']) ||
            empty($data['cart'])
        ) {
            throw new \Exception('Missing required fields');
        }
    }
}
