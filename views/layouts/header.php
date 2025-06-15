<header class="bg-blue-600 shadow">
    <nav class="container mx-auto flex items-center justify-between py-4 px-4">
        <a href="/" class="text-white text-2xl font-bold tracking-wide"><?php echo env('APP_NAME', 'ShopEasy'); ?></a>
        <ul class="flex items-center space-x-6">
            <li><a href="/" class="text-white hover:text-blue-200 transition">Home</a></li>
            <li><a href="/index.php?action=orders" class="text-white hover:text-blue-200 transition">My Orders</a></li>
            <li>
                <a href="/index.php?action=cart" class="relative text-white hover:text-blue-200 transition flex items-center"
                   x-data="navbarCart()" x-init="init()">
                    <svg class="w-6 h-6 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2 9m13-9l2 9m-5 0a2 2 0 11-4 0" />
                    </svg>
                    <span>Cart</span>
                    <span x-text="count" class="ml-1 text-xs bg-yellow-400 text-gray-800 rounded-full px-2 py-0.5 font-bold"></span>
                </a>
            </li>
        </ul>
    </nav>
</header>