<?php

ob_start();

$images = [
    [
        'src' => 'https://images.unsplash.com/photo-1465101046530-73398c7f28ca?auto=format&fit=crop&w=300&q=80',
        'alt' => 'Fashion',
    ],
    [
        'src' => 'https://images.unsplash.com/photo-1512436991641-6745cdb1723f?auto=format&fit=crop&w=300&q=80',
        'alt' => 'Electronics',
    ],
    [
        'src' => 'https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=300&q=80',
        'alt' => 'Lifestyle',
    ],
    [
        'src' => 'https://images.unsplash.com/photo-1454023492550-5696f8ff10e1?auto=format&fit=crop&w=300&q=80',
        'alt' => 'Home',
    ],
];
$appName = env('APP_NAME');
?>
<div class="relative bg-gradient-to-br from-blue-50 to-white min-h-[80vh] flex flex-col md:flex-row items-center justify-between px-8 py-16 gap-8">
  <div class="max-w-xl">
    <h1 class="text-5xl md:text-6xl font-extrabold text-gray-900 mb-6 leading-tight">
      Welcome to <span class="text-blue-600"><?= htmlspecialchars($appName) ?></span>
    </h1>
    <p class="text-xl text-gray-600 mb-8">
      Discover, shop, and pay seamlessly. Enjoy a modern, secure shopping experience with instant checkout and order tracking.
    </p>
    <div class="flex gap-4 mb-8">
      <a href="/index.php?action=products" class="inline-block px-8 py-3 text-lg font-semibold bg-blue-600 text-white rounded shadow hover:bg-blue-700 transition">Start Shopping</a>
      <a href="/index.php?action=orders" class="inline-block px-8 py-3 text-lg font-semibold bg-white border border-blue-600 text-blue-600 rounded hover:bg-blue-50 transition">My Orders</a>
    </div>
    <!-- Cart Preview (Alpine.js, DRY, clean init) -->
    <div class="bg-white/80 shadow rounded-lg p-6 flex items-center gap-4 max-w-md"
         x-data="cartPreview()">
      <svg class="w-10 h-10 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2 9m13-9l2 9m-5 0a2 2 0 11-4 0"></path></svg>
      <div>
        <div class="font-bold text-lg" x-text="itemCountText"></div>
        <div class="text-gray-600 text-sm">Cart preview (qty × $10 demo)</div>
      </div>
      <div class="ml-auto text-lg font-bold text-blue-700" x-text="'<?php echo env('PAYTABS_CURRENCY') ?> ' + total.toFixed(2)"></div>
      <a href="/index.php?action=cart" class="ml-4 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">View Cart</a>
    </div>
  </div>
  <div class="flex-1 flex flex-col items-center">
    <img src="https://images.unsplash.com/photo-1512436991641-6745cdb1723f?auto=format&fit=crop&w=600&q=80" alt="Modern Shopping" class="rounded-3xl shadow-2xl w-full max-w-md mb-8">
    <div class="grid grid-cols-2 gap-4">
      <?php foreach ($images as $img): ?>
        <img src="<?= htmlspecialchars($img['src']) ?>" alt="<?= htmlspecialchars($img['alt']) ?>" class="rounded-xl shadow w-36 h-36 object-cover">
      <?php endforeach; ?>
    </div>
  </div>
</div>
<script>
function cartPreview() {
    return {
        cart: JSON.parse(localStorage.getItem('cart') || '{}'),
        count: 0,
        total: 0,
        get itemCountText() {
            return this.count + ' item' + (this.count === 1 ? '' : 's');
        },
        init() {
            this.updateCart();
        },
        updateCart() {
            this.count = Object.values(this.cart).reduce((a, b) => a + Number(b), 0);
            // Replace 10 with real product price lookup if needed
            this.total = Object.entries(this.cart).reduce((a, [id, qty]) => a + qty * 10, 0);
        }
    }
}
</script>
<?php
$content = ob_get_clean();
$title = 'Welcome';
include __DIR__ . '/layouts/app.php';
?>
