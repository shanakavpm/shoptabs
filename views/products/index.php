<?php
// Use output buffering to capture the cart landing content for the layout
ob_start();
?>

<div class="relative bg-gradient-to-br from-blue-50 to-white min-h-[60vh] rounded-xl shadow mb-10 flex flex-col md:flex-row items-center justify-between px-8 py-12 gap-8">
  <div class="max-w-xl">
    <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 mb-4 leading-tight">
      Build Your <span class="text-blue-600">Order</span>
    </h1>
    <p class="text-lg text-gray-600 mb-6">
      Select your favorite products and add them to your cart. Enjoy a smooth, modern shopping experience!
    </p>
  </div>
  <div class="flex-1 flex flex-col items-center">
    <img src="https://images.unsplash.com/photo-1512436991641-6745cdb1723f?auto=format&fit=crop&w=600&q=80" alt="Modern Shopping" class="rounded-2xl shadow-2xl w-full max-w-xs mb-2">
  </div>
</div>
<div x-data="cartComponent()" x-init="() => { cartOpen = false }" x-cloak>
    <!-- Floating Cart Button -->
    <button @click="cartOpen = true" class="fixed z-40 bottom-6 right-6 bg-blue-600 hover:bg-blue-700 text-white rounded-full shadow-lg w-16 h-16 flex items-center justify-center text-2xl focus:outline-none focus:ring-4 focus:ring-blue-300 transition">
      <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2 9m13-9l2 9m-5 0a2 2 0 11-4 0"></path></svg>
    </button>
    <!-- Cart Drawer -->
    <div x-show="cartOpen" class="fixed inset-0 z-50 flex justify-end" style="display: none;">
      <div @click="cartOpen = false" class="fixed inset-0 bg-black bg-opacity-30 transition-opacity"></div>
      <div class="relative bg-white w-full max-w-md h-full shadow-2xl p-6 flex flex-col overflow-y-auto animate-slide-in-right">
        <button @click="cartOpen = false" class="absolute top-4 right-4 text-gray-400 hover:text-gray-800 text-2xl focus:outline-none">&times;</button>
        <div class="text-2xl font-bold text-blue-700 mb-4 pt-4">Your Cart</div>
        <template x-if="cartItems.length > 0">
          <div>
            <table class="min-w-full text-left border mb-4">
              <thead>
                <tr class="border-b">
                  <th class="py-2 px-3">Product</th>
                  <th class="py-2 px-3">Qty</th>
                  <th class="py-2 px-3">Price (<?php echo env('PAYTABS_CURRENCY') ?>)</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                <template x-for="item in cartItems" :key="item.product.id">
                  <tr class="border-b">
                    <td class="py-2 px-3" x-text="item.product.name"></td>
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
            <a href="?action=checkout" class="w-full block bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded transition text-center">Checkout</a>
          </div>
        </template>
        <template x-if="cartItems.length === 0">
          <div class="text-gray-500 text-center mt-10">Your cart is empty.</div>
        </template>
      </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-8 mb-12">
        <template x-for="product in products" :key="product.id">
            <div class="bg-white rounded-2xl shadow-lg p-6 flex flex-col justify-between transition-transform hover:-translate-y-1 hover:shadow-2xl">
                <img :src="product.image" @error="event.target.src='https://placehold.co/300x200?text=' + encodeURIComponent(product.name ? product.name : 'Product')" alt="Product" class="rounded-xl mb-4 w-full h-36 object-cover">
                <div>
                    <h5 class="text-lg font-semibold mb-2" x-text="product.name"></h5>
                    <p class="text-gray-600 mb-2" x-text="product.description"></p>
                    <p class="text-blue-600 font-bold mb-2"><?php echo env('PAYTABS_CURRENCY') ?> <span x-text="product.price.toFixed(2)"></span></p>
                    <span class="inline-block bg-green-100 text-green-800 text-xs font-semibold px-3 py-1 rounded mb-2">In stock: <span x-text="product.quantity"></span></span>
                </div>
                <button class="bg-gradient-to-r from-green-400 to-green-600 hover:from-green-500 hover:to-green-700 text-white font-bold py-2 px-4 rounded-lg shadow transition-all duration-200 ease-in-out transform hover:scale-105 mt-2" @click="addToCart(product)">
                  <svg class="inline w-5 h-5 mr-1 -mt-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2 9m13-9l2 9m-5 0a2 2 0 11-4 0"></path></svg>
                  Add to Cart
                </button>
            </div>
        </template>
        <template x-if="products.length === 0">
            <div class="col-span-full">
                <div class="bg-yellow-100 text-yellow-800 rounded p-4 text-center">No products found.</div>
            </div>
        </template>
    </div>

</div>
<script>
function cartComponent() {
    return {
        cartOpen: false,
        products: <?php echo json_encode(array_map(function($p) {
            return [
                'id' => $p->id,
                'name' => $p->name,
                'description' => $p->description,
                'price' => (float)$p->price,
                'image' => $p->image,
                'quantity' => (int)$p->quantity
            ];
        }, $products)); ?>,
        cart: JSON.parse(localStorage.getItem('cart') || '{}'),
        get cartItems() {
            return Object.entries(this.cart).map(([id, qty]) => {
                const product = this.products.find(p => p.id == id);
                return product ? { product, qty } : null;
            }).filter(Boolean);
        },
        get total() {
            return this.cartItems.reduce((sum, item) => sum + item.product.price * item.qty, 0);
        },
        addToCart(product) {
            if (!this.cart[product.id]) this.cart[product.id] = 0;
            this.cart[product.id]++;
            this.saveCart();
            this.cartOpen = true;
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
</script>

<?php
$content = ob_get_clean();
$title = 'Shopping Cart';
// Include the layout and pass the captured content
include 'views/layouts/app.php';
?>

