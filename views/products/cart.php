<?php

ob_start();
?>
<div class="min-h-[70vh] flex flex-col items-center justify-center bg-gradient-to-br from-blue-50 to-white py-12 px-4">
  <div class="max-w-2xl w-full bg-white rounded-2xl shadow-xl p-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-8 text-center">Your Cart</h1>
    <div x-data="cartComponent()" x-init="cartItems.length" class="w-full">
      <template x-if="cartItems.length === 0">
        <div class="text-gray-500 text-center py-12">
          <svg class="mx-auto w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2 9m13-9l2 9m-5 0a2 2 0 11-4 0"></path></svg>
          Your cart is empty.<br>
          <a href="/index.php?action=products" class="inline-block mt-4 px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">Start Shopping</a>
        </div>
      </template>
      <template x-if="cartItems.length > 0">
        <div>
          <table class="min-w-full text-left border mb-6">
            <thead>
              <tr class="border-b">
                <th class="py-2 px-3">Product</th>
                <th class="py-2 px-3">Quantity</th>
                <th class="py-2 px-3">Price (<?php echo env('PAYTABS_CURRENCY') ?>)</th>
                <th class="py-2 px-3">Remove</th>
              </tr>
            </thead>
            <tbody>
              <template x-for="item in cartItems" :key="item.product.id">
                <tr class="border-b">
                  <td class="py-2 px-3 font-semibold flex items-center gap-2">
                    <img :src="item.product.image || 'https://placehold.co/40x40?text=+' + encodeURIComponent(item.product.name[0] || 'P')" alt="" class="w-10 h-10 rounded object-cover border">
                    <span x-text="item.product.name"></span>
                  </td>
                  <td class="py-2 px-3">
                    <button @click="decrement(item.product)" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-2 py-1 rounded">-</button>
                    <span class="mx-2" x-text="item.qty"></span>
                    <button @click="increment(item.product)" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-2 py-1 rounded">+</button>
                  </td>
                  <td class="py-2 px-3"><?php echo env('PAYTABS_CURRENCY') ?> <span x-text="(item.product.price * item.qty).toFixed(2)"></span></td>
                  <td class="py-2 px-3">
                    <button @click="removeFromCart(item.product)" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded">Remove</button>
                  </td>
                </tr>
              </template>
            </tbody>
            <tfoot>
              <tr>
                <th colspan="2" class="py-2 px-3 text-right">Total</th>
                <th colspan="2" class="py-2 px-3"><?php echo env('PAYTABS_CURRENCY') ?> <span x-text="total.toFixed(2)"></span></th>
              </tr>
            </tfoot>
          </table>
          <div class="flex justify-end">
            <a href="/index.php?action=checkout" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-8 rounded transition">Proceed to Checkout</a>
          </div>
        </div>
      </template>
    </div>
  </div>
</div>
<script>
// Use the same cartComponent as in create.php
function cartComponent() {
    return {
        products: [], // Not needed here, just for compatibility
        cart: JSON.parse(localStorage.getItem('cart') || '{}'),
        get cartItems() {
            // We'll use localStorage cart, but need product info
            // Try to get product info from sessionStorage (populated by order_create)
            let products = JSON.parse(sessionStorage.getItem('products') || '[]');
            return Object.entries(this.cart).map(([id, qty]) => {
                const product = products.find(p => p.id == id) || {id, name: 'Product #' + id, price: 10, image: ''};
                return { product, qty };
            }).filter(Boolean);
        },
        get total() {
            return this.cartItems.reduce((sum, item) => sum + item.product.price * item.qty, 0);
        },
        increment(product) {
            if (this.cart[product.id]) this.cart[product.id]++;
            this.saveCart();
        },
        decrement(product) {
            if (this.cart[product.id] > 1) {
                this.cart[product.id]--;
            } else {
                delete this.cart[product.id];
            }
            this.saveCart();
        },
        removeFromCart(product) {
            delete this.cart[product.id];
            this.saveCart();
        },
        saveCart() {
            localStorage.setItem('cart', JSON.stringify(this.cart));
        },
    };
}
// Save products to sessionStorage when coming from order_create
if (window.location.search.includes('action=cart')) {
    if (window.sessionStorage && window.localStorage) {
        if (!sessionStorage.getItem('products') && localStorage.getItem('cart')) {
            fetch('/index.php?action=products')
                .then(r => r.text())
                .then(html => {
                    const match = html.match(/products: (\[.*?\]),/s);
                    if (match) sessionStorage.setItem('products', match[1]);
                });
        }
    }
}
</script>
<?php
$content = ob_get_clean();
$title ='Cart';
include 'views/layouts/app.php';
