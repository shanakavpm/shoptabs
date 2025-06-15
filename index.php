<?php
// Basic configuration
session_start();
ini_set('date.timezone', 'Asia/Kolkata');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/includes/helpers.php';
require __DIR__ . '/includes/autoloader.php';

try {
    $pdo = require __DIR__ . '/includes/database.php';

    // Controllers
    $orderController   = new App\Controllers\OrderController($pdo);
    $productController = new App\Controllers\ProductController($pdo);
    $paymentController = new App\Controllers\PaymentController($pdo);
    $homeController    = new App\Controllers\HomeController($pdo);

    // Routing
    $action = $_GET['action'] ?? 'home';
    $id     = $_GET['id'] ?? null;

    // Helper: enforce POST
    $require_post = function() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method Not Allowed']);
            exit;
        }
    };

    // Route definitions
    $routes = [
        // Landing
        'home'         => fn() => $homeController->index(),

        // Orders
        'orders'       => fn() => $orderController->index(),
        'order_show'   => function() use ($orderController, $id) {
            if (!$id) throw new Exception('Invalid order ID');
            $orderController->show((int)$id);
        },
        'order_submit' => function() use ($orderController, $require_post) {
            $require_post();
            $orderController->storeAjax();
        },

        // Products
        'products'     => fn() => $productController->index(),
        'cart'         => fn() => $productController->cart(),

        // Payments
        'checkout'         => fn() => $paymentController->checkout(),
        'payment_success'  => fn() => $paymentController->success(),
        'payment_error'    => fn() => $paymentController->error(),
        'payment_return'   => fn() => $paymentController->return(),
        'paytabs_request'  => function() use ($paymentController, $require_post) {
            $require_post();
            $paymentController->paytabsRequest();
        },
    ];

    // Dispatch
    if (isset($routes[$action])) {
        $routes[$action]();
    } else {
        throw new Exception('Page not found', 404);
    }

} catch (Exception $e) {
    http_response_code($e->getCode() ?: 500);
    error_log($e->getMessage());
    require 'views/error.php';
}