<?php

$host = env('DB_HOST') ?: 'host.docker.internal';
$dbname = env('DB_DATABASE') ?: 'adphp';
$username = env('DB_USERNAME') ?: 'root';
$password = env('DB_PASSWORD') ?: '@Saberion123';
$port = env('DB_PORT') ?: '3306';

// Database connection configuration
$dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
];

try {
    // Establish PDO connection
    $pdo = new PDO($dsn, $username, $password, $options);
    // Test the connection
    $pdo->query("SELECT 1");
} catch (PDOException $e) {
    http_response_code(500);
    $msg = "Database connection failed: " . $e->getMessage() . "\n";
    $msg .= "DSN: $dsn\nUsername: $username\n";
    if (php_sapi_name() === 'cli') {
        fwrite(STDERR, $msg);
    } else {
        echo nl2br(htmlentities($msg));
    }
    exit(1);
}

return $pdo;
