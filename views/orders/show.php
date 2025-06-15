<?php
/**
 * orders/show.php - Order Detail Page
 * Shows order, items, payment, and refund details with modern UI.
 */
ob_start();
?>
<nav class="text-sm mb-4" aria-label="Breadcrumb">
  <ol class="list-reset flex text-gray-600">
    <li><a href="?action=orders" class="hover:underline text-blue-600">My Orders</a></li>
    <li><span class="mx-2">/</span></li>
    <li class="text-gray-800 font-semibold">Order #<?php echo htmlspecialchars($order->id ?? ''); ?></li>
  </ol>
</nav>
<h1 class="text-2xl font-bold mb-6">Order Details</h1>
<?php if (!empty($order)): ?>
<div class="mb-6 p-4 bg-gray-50 rounded border">
    <div><strong>Order #:</strong> <?php echo htmlspecialchars($order->id); ?></div>
    <div><strong>Customer:</strong> <?php echo htmlspecialchars($order->customer_name); ?> (<?php echo htmlspecialchars($order->customer_email); ?>)</div>
    <div><strong>Status:</strong> <?php echo status_badge($order->status); ?></div>
    <div><strong>Date:</strong> <?php echo htmlspecialchars($order->created_at); ?></div>
</div>
<h2 class="text-xl font-semibold mb-2">Order Items</h2>
<table class="min-w-full bg-white border rounded mb-6">
    <thead>
        <tr>
            <th class="px-4 py-2 border">Product</th>
            <th class="px-4 py-2 border">Description</th>
            <th class="px-4 py-2 border">Quantity</th>
            <th class="px-4 py-2 border">Price (<?php echo env('PAYTABS_CURRENCY'); ?>)</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($order_items as $item): ?>
        <tr>
            <td class="px-4 py-2 border flex items-center gap-2">
                <?php if (!empty($item->product_image)): ?>
                    <img src="<?php echo htmlspecialchars($item->product_image); ?>" alt="<?php echo htmlspecialchars($item->product_name); ?>" class="w-10 h-10 rounded object-cover border" />
                <?php endif; ?>
                <span class="font-semibold"><?php echo htmlspecialchars($item->product_name ?? $item->product_id); ?></span>
            </td>
            <td class="px-4 py-2 border text-sm text-gray-700">
                <?php echo htmlspecialchars($item->product_description ?? ''); ?>
            </td>
            <td class="px-4 py-2 border text-center"><?php echo htmlspecialchars($item->quantity); ?></td>
            <td class="px-4 py-2 border text-right"><?php echo number_format($item->price, 2); ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <th colspan="3" class="px-4 py-2 border text-right bg-gray-50 font-bold">Total</th>
            <th class="px-4 py-2 border text-right bg-gray-50 font-bold">
                <?php echo number_format($order->total_amount ?? 0, 2); ?>
            </th>
        </tr>
    </tfoot>
</table>
<h2 class="text-xl font-semibold mb-2">Payment Request Payload</h2>
<pre class="bg-gray-100 p-2 rounded border overflow-x-auto text-xs">
<?php
if ($payment && isset($payment->payment_request_payload)) {
    echo htmlspecialchars(json_encode(json_decode($payment->payment_request_payload), JSON_PRETTY_PRINT));
} else {
    echo 'N/A';
}
?></pre>
<h2 class="text-xl font-semibold mb-2">Payment Response Payload</h2>
<pre class="bg-gray-100 p-2 rounded border overflow-x-auto text-xs">
<?php
if ($payment && isset($payment->payment_response_payload)) {
    echo htmlspecialchars(json_encode(json_decode($payment->payment_response_payload), JSON_PRETTY_PRINT));
} else {
    echo 'N/A';
}
?></pre>
<?php if (!empty($refunds)): ?>
<h2 class="text-xl font-semibold mb-2">Refund Requests</h2>
<?php foreach ($refunds as $refund): ?>
    <div class="mb-4 p-2 bg-gray-50 border rounded">
        <div class="font-semibold">Refund #<?php echo htmlspecialchars($refund->id); ?> (<?php echo htmlspecialchars($refund->created_at); ?>)</div>
        <div class="mt-1 text-sm text-gray-600">Request:</div>
        <pre class="bg-gray-100 p-2 rounded border overflow-x-auto text-xs"><?php echo htmlspecialchars(json_encode(json_decode($refund->request_payload), JSON_PRETTY_PRINT)); ?></pre>
        <div class="mt-1 text-sm text-gray-600">Response:</div>
        <pre class="bg-gray-100 p-2 rounded border overflow-x-auto text-xs"><?php echo htmlspecialchars(json_encode(json_decode($refund->response_payload), JSON_PRETTY_PRINT)); ?></pre>
    </div>
<?php endforeach; ?>
<?php endif; ?>
<?php else: ?>
<div class="text-gray-500">Order not found.</div>
<?php endif; ?>
<?php
$content = ob_get_clean();
$title = 'Order Details';
include __DIR__ . '/../layouts/app.php';
?>
