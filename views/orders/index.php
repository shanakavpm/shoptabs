<?php
/**
 * orders/index.php - Orders List Page
 * Lists all orders for the user with status, details, and actions.
 */
ob_start();
?>
<h1 class="text-2xl font-bold mb-6">My Orders</h1>
<?php if (!empty($orders)): ?>
<table class="min-w-full bg-white border rounded shadow">
    <thead>
        <tr>
            <th class="px-4 py-2 border">Order #</th>
            <th class="px-4 py-2 border">Customer</th>
            <th class="px-4 py-2 border">Email</th>
            <th class="px-4 py-2 border">Status</th>
            <th class="px-4 py-2 border">Date</th>
            <th class="px-4 py-2 border">Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($orders as $order): ?>
        <tr>
            <td class="px-4 py-2 border text-center"><?php echo htmlspecialchars($order->id); ?></td>
            <td class="px-4 py-2 border"><?php echo htmlspecialchars($order->customer_name); ?></td>
            <td class="px-4 py-2 border"><?php echo htmlspecialchars($order->customer_email); ?></td>
            <td class="px-4 py-2 border text-center">
                <?php echo status_badge($order->status); ?>
            </td>
            <td class="px-4 py-2 border text-center"><?php echo htmlspecialchars($order->created_at); ?></td>
            <td class="px-4 py-2 border text-center">
                <a href="?action=order_show&id=<?php echo urlencode($order->id); ?>" class="text-blue-600 underline">View</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php else: ?>
<div class="text-gray-500">No orders found.</div>
<?php endif; ?>
<?php
$content = ob_get_clean();
$title = 'Orders';
include __DIR__ . '/../layouts/app.php';
?>
