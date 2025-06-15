<?php
require __DIR__ . '/../includes/helpers.php';
$pdo = require __DIR__ . '/../includes/database.php';
require __DIR__ . '/../vendor/autoload.php';

try {
    $faker = Faker\Factory::create();
    $clothingItems = [
        'T-Shirt', 'Hoodie', 'Sweatshirt', 'Jeans', 'Shorts', 'Jacket', 'Blazer', 'Dress', 'Skirt', 'Polo Shirt',
        'Tank Top', 'Cardigan', 'Sweater', 'Chinos', 'Tracksuit', 'Leggings', 'Coat', 'Puffer Jacket', 'Denim Jacket',
        'Cargo Pants', 'Overalls', 'Shirt', 'Blouse', 'Suit', 'Vest', 'Windbreaker', 'Raincoat', 'Parka', 'Poncho',
        'Joggers', 'Capri Pants', 'Dungarees', 'Romper', 'Camisole', 'Tunic', 'Kimono', 'Anorak', 'Peacoat', 'Bomber Jacket'
    ];

    $stmt = $pdo->prepare("INSERT INTO products (name, slug, price, description, sku, quantity, image) VALUES (?, ?, ?, ?, ?, ?, ?)");

    $sampleImageUrl = 'https://dummyimage.com/400x400/cccccc/000000&text=T-Shirt';

    foreach (range(1, 9) as $i) {
        $item = $clothingItems[array_rand($clothingItems)];
        $name =  ucfirst($faker->word) . ' ' . $item;
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
        $stmt->execute([
            $name,
            $slug,
            $faker->randomFloat(2, 1, 20),
            $faker->sentence(rand(6, 12)),
            strtoupper(substr($slug, 0, 8)) . rand(1000, 9999),
            rand(50, 200),
            $sampleImageUrl
        ]);
        echo "✅ Added: $name\n";
    }

    echo "\n✅ Successfully added 9 demo products!\n";
    
} catch (PDOException $e) {
    die("❌ Error: " . $e->getMessage() . "\n");
}
