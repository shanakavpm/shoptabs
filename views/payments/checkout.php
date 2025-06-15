<?php
// orders/checkout.php - Checkout form with shipping/pickup and payment iframe
ob_start();
?>
<div class="relative bg-gradient-to-br from-blue-50 to-white py-10 px-2 md:px-8 rounded-xl shadow mb-10">
  <div class="max-w-4xl mx-auto">
    <div class="flex items-center justify-center gap-4 mb-10">
      <div class="flex items-center">
        <span class="w-8 h-8 flex items-center justify-center rounded-full bg-blue-600 text-white font-bold">1</span>
        <span class="ml-2 font-semibold text-blue-700">Cart</span>
      </div>
      <span class="w-8 h-1 bg-blue-200 rounded mx-2"></span>
      <div class="flex items-center">
        <span class="w-8 h-8 flex items-center justify-center rounded-full bg-blue-600 text-white font-bold">2</span>
        <span class="ml-2 font-semibold text-blue-700">Checkout</span>
      </div>
      <span class="w-8 h-1 bg-blue-200 rounded mx-2"></span>
      <div class="flex items-center">
        <span class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-300 text-gray-500 font-bold">3</span>
        <span class="ml-2 font-semibold text-gray-500">Payment</span>
      </div>
    </div>
    <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-8 text-center">Checkout</h1>
  </div>
</div>
<div x-data="checkoutComponent()" x-init="syncCart()" class="max-w-6xl mx-auto pb-12">


<script src="https://secure-global.paytabs.com/payment/js/paylib.js"></script>
  
<div class="grid grid-cols-1 md:grid-cols-2 gap-16">
  <!-- Left: Customer/Cart Details -->
  <form @submit.prevent="submitOrder" class="bg-white shadow-2xl rounded-2xl px-10 pt-8 pb-12 mb-4 flex flex-col space-y-6">
    <h2 class="text-2xl font-bold mb-2">Customer Details</h2>
  <div class="grid grid-cols-2 gap-4 mb-2">
    <div>
      <label class="block text-gray-700">Name</label>
      <input type="text" x-model="customer_name" class="mt-1 block w-full border rounded px-2 py-1" required>
    </div>
    <div>
      <label class="block text-gray-700">Email</label>
      <input type="email" x-model="customer_email" class="mt-1 block w-full border rounded px-2 py-1" required>
    </div>
    <div>
      <label class="block text-gray-700">Phone</label>
      <input type="text" x-model="customer_phone" class="mt-1 block w-full border rounded px-2 py-1">
    </div>
    <div>
      <label class="block text-gray-700">Street</label>
      <input type="text" x-model="customer_street" class="mt-1 block w-full border rounded px-2 py-1">
    </div>
    <div>
      <label class="block text-gray-700">Country</label>
      <select x-model="customer_country" class="mt-1 block w-full border rounded px-2 py-1" required>
        <option value="EG">Egypt</option>
        <!-- <option value="AE">UAE</option>
        <option value="US">United States</option> -->
      </select>
    </div>
    <div>
      <label class="block text-gray-700">City</label>
      <input type="text" x-model="customer_city" class="mt-1 block w-full border rounded px-2 py-1">
    </div>
    <div>
      <label class="block text-gray-700">State</label>
      <input type="text" x-model="customer_state" class="mt-1 block w-full border rounded px-2 py-1">
    </div>
    <div>
      <label class="block text-gray-700">Zip Code</label>
      <input type="text" x-model="customer_zip" class="mt-1 block w-full border rounded px-2 py-1">
    </div>
  </div>
  <div class="mb-4 flex items-center">
    <input type="checkbox" x-model="shipping_same_as_billing" class="mr-2">
    <label class="text-gray-700">Shipping Same As Billing</label>
  </div>



<div class="mb-4">
  <span class="block text-gray-700 mb-2">Choose a Payment Option</span>
  <label class="inline-flex items-center mr-6">
    <input type="radio" x-model="payment_option" value="paytabs" class="form-radio text-blue-600">
    <span class="ml-2 flex items-center">PayTabs</span>
  </label>
  <label class="inline-flex items-center">
    <input type="radio" x-model="payment_option" value="cod" class="form-radio text-blue-600">
    <span class="ml-2 flex items-center">Cash on Delivery</span>
  </label>
</div>

<button
  type="submit"
  :disabled="!payment_option || (payment_option === 'paytabs' && showPayTabsIframe)"
  :class="['w-full bg-gradient-to-r from-blue-600 to-blue-800 text-white py-3 mt-4 rounded-lg font-bold text-lg shadow hover:from-blue-700 hover:to-blue-900 transition-all', (!payment_option || (payment_option === 'paytabs' && showPayTabsIframe)) ? 'opacity-60 cursor-not-allowed' : '']"
>
  Proceed to Payment
</button>
<!-- Loader Overlay -->
<div x-show="loading" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40" style="display: none;">
  <div class="bg-white rounded-lg shadow-lg p-8 flex flex-col items-center">
    <svg class="animate-spin h-10 w-10 text-blue-600 mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
    </svg>
    <div class="text-blue-700 font-semibold text-lg">Processing... Please wait</div>
  </div>
</div>


  </form>
  <!-- Right: Payment Details/iFrame -->
  <div class="bg-white shadow rounded px-8 pt-6 pb-8 mb-4 flex flex-col min-h-[400px]">
    <template x-if="payment_option === 'paytabs' && showPayTabsIframe && paytabsIframeUrl">
      <div>
        <h2 class="text-xl font-semibold mb-2 mt-4 md:mt-0">Complete the payment:</h2>
        <div id="paytabs-iframe-container" class="my-6 flex-1">
          <iframe :src="paytabsIframeUrl" width="100%" height="100%" frameborder="0" allow="payment" id="paytabs-payment-iframe" title="PayTabs Payment" class="w-full min-w-[400px] min-h-[600px] rounded shadow-lg border"></iframe>

        </div>
      </div>
    </template>
    <template x-if="payment_option !== 'paytabs' || !showPayTabsIframe">
      <div class="text-gray-500 text-center my-10">Payment form will appear here after you proceed to payment with PayTabs.</div>
    </template>
  </div>
</div>

<script>
function checkoutComponent() {
    return {
        loading: false,
        payment_option: '',
        showPayTabsIframe: false,
        paytabsIframeUrl: '',
        products: <?php echo json_encode(array_map(function($p) {
            return [
                'id' => $p->id,
                'name' => $p->name,
                'description' => $p->description,
                'price' => (float)$p->price
            ];
        }, $products ?? [])); ?>,
        cart: {},
        customer_name: '',
        customer_email: '',
        customer_phone: '',
        customer_street: '',
        customer_country: 'EG',
        customer_city: '',
        customer_state: '',
        customer_zip: '',
        shipping_same_as_billing: true,
        shipping_type: 'pickup',
        shipping_address: '',
        card_number: '',
        card_expiry_month: '',
        card_expiry_year: '',
        card_cvv: '',
        syncCart() {
            this.cart = JSON.parse(localStorage.getItem('cart') || '{}');
        },
        get cartItems() {
            return Object.entries(this.cart).map(([id, qty]) => {
                const product = this.products.find(p => p.id == id);
                return product ? { product, qty } : null;
            }).filter(Boolean);
        },
        get total() {
            return this.cartItems.reduce((sum, item) => sum + item.product.price * item.qty, 0);
        },
        async submitOrder() {
            // Basic client-side validation
            if (!this.customer_name || !this.customer_email || this.cartItems.length === 0) {
                alert('Please fill all fields and ensure your cart is not empty.');
                return;
            }
            this.loading = true;
            this.error = '';
            try {
                const response = await fetch('?action=order_submit', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        customer_name: this.customer_name,
                        customer_email: this.customer_email,
                        customer_phone: this.customer_phone,
                        customer_street: this.customer_street,
                        customer_country: this.customer_country,
                        customer_city: this.customer_city,
                        customer_state: this.customer_state,
                        customer_zip: this.customer_zip,
                        shipping_same_as_billing: this.shipping_same_as_billing,
                        shipping_type: this.shipping_type,
                        shipping_address: this.shipping_address,
                        card_number: this.card_number,
                        card_expiry_month: this.card_expiry_month,
                        card_expiry_year: this.card_expiry_year,
                        card_cvv: this.card_cvv,
                        cart: this.cart
                    })
                });
                const data = await response.json();
                if (!data.success) {
                    this.error = data.error || 'Order failed.';
                    alert(this.error);
                } else {
                    // Order success: do NOT clear cart here. Only clear after payment is successful.
                    // Now initiate PayTabs payment request
                    const paytabsRes = await fetch('?action=paytabs_request', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: 'order_id=' + encodeURIComponent(data.order_id)
                    });
                    const paytabsData = await paytabsRes.json();
                    console.log('PayTabs backend response:', paytabsData);
                    if (this.payment_option === 'paytabs' && paytabsData.success && paytabsData.paytabs_response && paytabsData.paytabs_response.redirect_url) {
                        // Embed PayTabs iFrame dynamically (no redirect)
                        this.embedPayTabsIframe(paytabsData.paytabs_response.redirect_url);
                    } else if (this.payment_option === 'cod') {
                        this.showPayTabsIframe = false;
                        this.paytabsIframeUrl = '';
                        alert('Order placed with Cash on Delivery!');
                        // Optionally redirect or show a thank you message here
                    } else {
                        this.error = paytabsData.error || 'Payment initiation failed.';
                        alert(this.error);
                    }
                }
            } catch (e) {
                this.error = 'Network or server error.';
                alert(this.error);
            } finally {
                this.loading = false;
            }
        },
        // Instead of injecting iframe, set state for dynamic rendering
        embedPayTabsIframe(url) {
            this.paytabsIframeUrl = url;
            this.showPayTabsIframe = true;
            
            // Listen for postMessage from PayTabs
            window.addEventListener('message', function(event) {
                if (!event.origin.includes('paytabs.com')) return;
                const message = event.data;
                if (typeof message === 'object' && message.event) {
                    if (message.event === 'success') {
                        window.location.href = '?action=success';
                    } else if (message.event === 'error' || message.event === 'failed') {
                        window.location.href = '?action=error';
                    }
                }
            }, { once: true });
        }
    }
}
</script>
<?php
$title = 'Payment Page';
$content = ob_get_clean();
include 'views/layouts/app.php';
?>
