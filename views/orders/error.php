<?php
/**
 * orders/error.php - Payment Error Page
 * Shows a styled error message after failed payment.
 */
ob_start();
$error_msg = $error ?? 'There was a problem processing your payment. Please try again.';
$tran_ref = $paytabs['tranRef'] ?? '';
$billing_name = $paytabs['customer_details']['name'] ?? ($paytabs['customer_name'] ?? '');
?>
<div class="min-h-[60vh] flex items-center justify-center bg-gray-50 px-2">
  <div class="bg-white border border-red-100 rounded-xl shadow-lg p-5 w-full max-w-sm text-center">
    <div class="flex justify-center mb-3">
      <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="none"/>
        <path d="M12 8v4m0 4h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
      </svg>
    </div>
    <h1 class="text-xl font-bold text-red-600 mb-1">Something went wrong</h1>
    <p class="text-gray-700 mb-3">An error occurred. Please try again or contact support.</p>
    <div class="bg-red-50 border border-red-200 rounded p-2 mb-3">
      <pre class="text-xs text-red-700 whitespace-pre-wrap"><?php echo (isset($e) ? htmlspecialchars($e->getMessage()) : 'No error details.'); ?></pre>
    </div>
    <a href="/" class="mt-2 inline-block bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-6 rounded transition shadow">Return Home</a>
  </div>
</div>
<?php
$content = ob_get_clean();
$title = 'Payment Error';
include __DIR__ . '/../layouts/app.php';
?>

