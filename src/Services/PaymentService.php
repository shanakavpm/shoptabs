<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Order;
use PDO;

class PaymentService
{
    protected $pdo;
    protected $paymentModel;
    protected $orderModel;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->paymentModel = new Payment($pdo);
        $this->orderModel = new Order($pdo);
    }



    public function buildCustomerArray($order)
    {
        return [
            'name' => $order->customer_name,
            'email' => $order->customer_email,
            'street1' => $order->shipping_address,
            'city' => $order->shipping_city ?? '',
            'country' => $order->shipping_country ?? 'EG',
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
        ];
    }

    public function buildPayTabsPayload($order, $cart_amount, $customer)
    {
        return [
            "profile_id" => env('PAYTABS_PROFILE_ID'),
            "tran_type" => "sale",
            "tran_class" => "ecom",
            "cart_id" => 'order-' . $order->id . '-' . substr(bin2hex(random_bytes(6)), 0, 8),
            "cart_description" => "Order #" . $order->id,
            "cart_currency" => env('PAYTABS_CURRENCY'),
            "cart_amount" => $cart_amount,
            "return" => env('PAYTABS_RETURN_URL'),
            "customer_details" => $customer,
            "framed" => true,
            "framed_return_parent" => true,
            "framed_message_target" => $_SERVER['HTTP_ORIGIN'],
            "hide_shipping" => true,
        ];
    }

    public function initiatePayTabsRequest($order_id)
    {
        $order = $this->orderModel->find($order_id);
        if (!$order) {
            throw new \Exception('Order not found');
        }
        $customer = $this->buildCustomerArray($order);
        $payload = $this->buildPayTabsPayload($order, $order->total_amount, $customer);
        $ch = curl_init(env('PAYTABS_API_URL'));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'authorization: ' . env('PAYTABS_SERVER_KEY'),
            'content-type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $this->paymentModel->updateStatus($order_id, 'pending', json_encode($payload), null);
        return [$httpcode, $result];
    }
}
