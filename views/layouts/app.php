<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo env('APP_NAME')." -  ".$title ?? ''; ?></title>
    <link rel="icon" href="/public/favicon.ico" type="image/x-icon">
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">
    <?php include 'header.php'; ?>
    <main class="flex-1 flex items-center justify-center">
        <div class="w-full max-w-7xl mx-auto p-10 bg-white shadow-lg">
            <?php echo $content; ?>
        </div>
    </main>
    <?php include 'footer.php'; ?>
<script>
function navbarCart() {
    return {
        count: 0,
        init() {
            this.update();
            window.addEventListener('storage', (e) => {
                if (e.key === 'cart') this.update();
            });
        },
        update() {
            this.count = Object.values(JSON.parse(localStorage.getItem('cart') || '{}')).reduce((a,b)=>a+Number(b),0);
        }
    }
}
</script>
</body>
</html>