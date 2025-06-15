<?php

use PHPUnit\Framework\TestCase;
use App\Services\OrderService;
use App\Models\Order;
use App\Models\Product;

require_once __DIR__ . '/../src/Services/OrderService.php';
require_once __DIR__ . '/../src/Models/Order.php';
require_once __DIR__ . '/../src/Models/Product.php';

class OrderServiceTest extends TestCase
{
    protected $orderService;

    protected $pdo;

    protected function setUp(): void
    {
        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS orders (id INTEGER PRIMARY KEY AUTOINCREMENT, customer_name TEXT, customer_email TEXT, customer_phone TEXT, shipping_address TEXT, shipping_city TEXT, shipping_country TEXT, total_amount REAL, status TEXT, created_at TEXT)");
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS products (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT, price REAL, quantity INTEGER)");
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS order_items (id INTEGER PRIMARY KEY AUTOINCREMENT, order_id INTEGER, product_id INTEGER, quantity INTEGER, price REAL)");
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS payments (id INTEGER PRIMARY KEY AUTOINCREMENT, order_id INTEGER, amount REAL, status TEXT, payment_request_payload TEXT, payment_response_payload TEXT, created_at TEXT, updated_at TEXT)");
        $this->orderService = new OrderService($this->pdo);
    }

    public function testCreateOrderWithItemsAndPayment()
    {
        // Insert product into database
        $this->pdo->exec("INSERT INTO products (id, name, price, quantity) VALUES (1, 'Test Product', 10.00, 5)");
        $cart = [1 => 2];
        $orderData = [
            'customer_name' => 'Test User',
            'customer_email' => 'test@example.com',
            'customer_phone' => '1234567890',
            'shipping_address' => 'Test Street',
            'shipping_city' => 'Cairo',
            'shipping_country' => 'EG',
            'status' => 'pending',
            'created_at' => date('Y-m-d H:i:s')
        ];
        $order = $this->orderService->createOrderWithItemsAndPayment($orderData, $cart);
        $this->assertIsNumeric($order);
    }

    public function testDecrementProductQuantity()
    {
        // Insert product into database
        $this->pdo->exec("INSERT INTO products (id, name, price, quantity) VALUES (1, 'Test Product', 10.00, 5)");
        $product = new Product($this->pdo);
        $product->decrementQuantity(1, 2);
        $stmt = $this->pdo->prepare('SELECT quantity FROM products WHERE id = 1');
        $stmt->execute();
        $qty = $stmt->fetchColumn();
        $this->assertEquals(3, $qty);
    }
}
