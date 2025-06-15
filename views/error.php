<?php

ob_start();
?>
<div class="min-h-screen flex items-center justify-center bg-gray-50 px-2">
  <div class="bg-white rounded-lg shadow p-4 w-full max-w-xs text-center">
    <div class="flex justify-center mb-2">
      <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="none"/>
        <path d="M12 8v4m0 4h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
      </svg>
    </div>
    <h1 class="text-lg font-bold text-red-600 mb-1">Error</h1>
    <p class="text-gray-700 text-sm mb-2">Something went wrong. Please try again.</p>
    <div class="bg-red-50 border border-red-200 rounded p-2 mb-3">
      <pre class="text-xs text-red-700 whitespace-pre-wrap"><?php echo (isset($e) ? htmlspecialchars($e->getMessage()) : 'No error details.'); ?></pre>
    </div>
    <a href="/" class="mt-2 inline-block bg-red-600 hover:bg-red-700 text-white text-sm font-semibold py-1.5 px-4 rounded transition">Return Home</a>
  </div>
</div>

<?php
$content = ob_get_clean();
$title = 'Error';
include __DIR__ . '/layouts/app.php';
?>
