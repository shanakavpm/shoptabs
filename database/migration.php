<?php
// Include database config
require __DIR__ . '/../includes/helpers.php';
$pdo = require __DIR__ . '/../includes/database.php';

try {
    
    // SQL to create and populate the table
    $sql = <<<SQL
    DROP TABLE IF EXISTS `refunds`;
    DROP TABLE IF EXISTS `payments`;
    DROP TABLE IF EXISTS `order_items`;
    DROP TABLE IF EXISTS `orders`;
    DROP TABLE IF EXISTS `products`;
    
    CREATE TABLE `products` (
      `id` INT NOT NULL AUTO_INCREMENT,
      `name` VARCHAR(255) NOT NULL,
      `slug` VARCHAR(255) NOT NULL,
      `price` DECIMAL(10,2) NOT NULL,
      `description` TEXT,
      `image` TEXT,
      `sku` VARCHAR(255) NOT NULL,
      `quantity` INT,
      `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    CREATE TABLE `orders` (
      `id` INT NOT NULL AUTO_INCREMENT,
      `customer_name` VARCHAR(255) NOT NULL,
      `customer_email` VARCHAR(255) NOT NULL,
      `customer_phone` VARCHAR(50),
      `shipping_address` TEXT,
      `shipping_city` VARCHAR(100),
      `shipping_country` VARCHAR(10),
      `total_amount` DECIMAL(10,2) DEFAULT 0.00,
      `status` VARCHAR(32) NOT NULL DEFAULT 'pending',
      `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    CREATE TABLE `order_items` (
      `id` INT NOT NULL AUTO_INCREMENT,
      `order_id` INT NOT NULL,
      `product_id` INT NOT NULL,
      `quantity` INT NOT NULL,
      `price` DECIMAL(10,2) NOT NULL,
      `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
      FOREIGN KEY (`product_id`) REFERENCES `products`(`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    CREATE TABLE `payments` (
      `id` INT NOT NULL AUTO_INCREMENT,
      `order_id` INT NOT NULL,
      `payment_request_payload` JSON,
      `payment_response_payload` JSON,
      `status` VARCHAR(32) NOT NULL DEFAULT 'pending',
      `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    CREATE TABLE `refunds` (
      `id` INT NOT NULL AUTO_INCREMENT,
      `payment_id` INT NOT NULL,
      `refund_request_payload` JSON,
      `refund_response_payload` JSON,
      `status` VARCHAR(32) NOT NULL DEFAULT 'pending',
      `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      FOREIGN KEY (`payment_id`) REFERENCES `payments`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    SQL;
    
    // Execute SQL
    $pdo->exec($sql);
    
    echo "✅ Migration completed successfully!\n";
    
} catch (PDOException $e) {
    die("❌ Migration failed: " . $e->getMessage() . "\n");
}
?>