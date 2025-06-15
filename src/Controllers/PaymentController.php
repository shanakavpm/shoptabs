<?php

namespace App\Controllers;

use App\Models\Payment;
use App\Models\Refund;
use App\Models\Product;
use App\Models\OrderItem;
use App\Models\Order;
use App\Services\PaymentService;

class PaymentController extends Controller
{
    protected $payment;
    protected $refund;

    public function __construct($pdo)
    {
        parent::__construct($pdo);
        $this->payment = new Payment($pdo);
        $this->refund = new Refund($pdo);
    }

    public function checkout()
    {
        // Fetch all products for cart summary on checkout page
        $productModel = new Product($this->pdo);
        $products = $productModel->all();
        return $this->renderView('payments/checkout', ['products' => $products]);
    }

    /**
     * Handle PayTabs payment return
     * Validates the redirect signature and shows result
     */
    public function return()
    {
        $serverKey = env('PAYTABS_SERVER_KEY');
        $post = $_POST ?: $_GET;
        if (!is_valid_redirect($post, $serverKey)) {
            return $this->renderView('orders/error', ['paytabs' => $post, 'error' => 'Invalid payment signature.']);
        }

        // Get order ID from cart_id or reference
        $cart_id = $post['cart_id'] ?? ($post['cartId'] ?? null);
        $order_id = null;
        if ($cart_id && preg_match('/order-(\d+)-/', $cart_id, $m)) {
            $order_id = $m[1];
        }

        if ($order_id) {
            // Update payment status and response
            $paymentModel = new Payment($this->pdo);
            $payment = $paymentModel->findByOrder($order_id);
            if ($payment) {
                $stmt = $this->pdo->prepare('UPDATE payments SET status = ?, payment_response_payload = ? WHERE id = ?');
                $stmt->execute(['completed', json_encode($post), $payment->id]);
            }
            // Update order status
            $orderModel = new Order($this->pdo);
            $order = $orderModel->find($order_id);
            if ($order) {
                // Deduct inventory for each order item
                $orderItemModel = new OrderItem($this->pdo);
                $items = $orderItemModel->whereOrder($order_id);
                $productModel = new Product($this->pdo);
                foreach ($items as $item) {
                    try {
                        $productModel->decrementQuantity($item->product_id, $item->quantity);
                    } catch (\Exception $e) {
                        // Optionally log or handle insufficient stock
                    }
                }
                $stmt = $this->pdo->prepare('UPDATE orders SET status = ? WHERE id = ?');
                $stmt->execute(['completed', $order_id]);
            }
        }
        echo "<script>localStorage.removeItem('cart');sessionStorage.removeItem('products');</script>";
        return $this->renderView('orders/success', ['paytabs' => $post]);
    }

    // Endpoint to initiate PayTabs payment request
    public function paytabsRequest()
    {
        $order_id = $_POST['order_id'] ?? null;
        if (!$order_id) {
            echo json_encode(['success' => false, 'error' => 'Order ID required']);
            exit;
        }
        $paymentService = new PaymentService($this->pdo);
        try {
            list($httpcode, $result) = $paymentService->initiatePayTabsRequest($order_id);
            if ($httpcode == 200) {
                $response = json_decode($result, true);
                echo json_encode(['success' => true, 'paytabs_response' => $response]);
            } else {
                echo json_encode(['success' => false, 'error' => 'PayTabs request failed', 'raw' => $result]);
            }
        } catch (\Exception $e) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit;
    }
}
