<?php
/**
 * orders/success.php - Payment Success Page
 * Shows a styled confirmation after successful payment.
 */
ob_start();
$tran_ref = $paytabs['tranRef'] ?? '';
$billing_name = $paytabs['customer_details']['name'] ?? ($paytabs['customer_name'] ?? '');
?>
<div class="min-h-[60vh] flex items-center justify-center bg-gray-50 px-2">
  <div class="bg-white border border-green-100 rounded-xl shadow-lg p-5 w-full max-w-sm text-center">
    <div class="flex justify-center mb-3">
      <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="none" />
        <path d="M8 12l3 3l5-5" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" />
      </svg>
    </div>
    <h2 class="text-xl font-bold text-green-600 mb-1">Payment Successful</h2>
    <p class="text-gray-700 mb-3">Your payment was processed and your order is confirmed.</p>
    <?php if ($tran_ref): ?>
      <div class="text-gray-900 font-mono text-base mb-2">Ref: <?php echo htmlspecialchars($tran_ref); ?></div>
    <?php endif; ?>
    <?php if ($billing_name): ?>
      <div class="text-gray-500 text-sm mb-3">Billed to: <span class="font-semibold text-gray-700"><?php echo htmlspecialchars($billing_name); ?></span></div>
    <?php endif; ?>
    <a href="/index.php?action=orders" class="mt-2 inline-block bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-6 rounded transition shadow">View My Orders</a>
  </div>
</div>
<?php
$content = ob_get_clean();
$title = 'Payment Success';
include __DIR__ . '/../layouts/app.php';
?>
