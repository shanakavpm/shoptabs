<?php

namespace Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use PDO;
use PDOException;

abstract class TestCase extends BaseTestCase
{
    protected static PDO $pdo;
    protected static $testDbFile = __DIR__ . '/test_db.sqlite';

    public static function setUpBeforeClass(): void
    {
        // Set up in-memory SQLite database for testing
        self::$pdo = new PDO('sqlite::memory:');
        self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Create tables
        self::createTables();
        
        // Mock global functions
        self::mockGlobalFunctions();
    }
    
    protected static function mockGlobalFunctions(): void
    {
        // Mock flash function
        if (!function_exists('flash')) {
            eval('function flash($message, $type = "success") {
                $_SESSION["flash"] = ["message" => $message, "type" => $type];
                return true;
            }');
        }
        
        // Mock getFlash function
        if (!function_exists('getFlash')) {
            eval('function getFlash() {
                $flash = $_SESSION["flash"] ?? null;
                unset($_SESSION["flash"]);
                return $flash;
            }');
        }
        
        // Mock redirect function
        if (!function_exists('redirect')) {
            eval('function redirect($location) {
                return "Redirected to: $location";
            }');
        }
        
        // Mock back function
        if (!function_exists('back')) {
            eval('function back() {
                return "Redirected back";
            }');
        }
        
        // Mock request function
        if (!function_exists('request')) {
            eval('function request($key, $default = null) {
                return $_REQUEST[$key] ?? $default;
            }');
        }
    }

    protected static function createTables(): void
    {
        $sql = <<<SQL
        CREATE TABLE IF NOT EXISTS advertisements (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title VARCHAR(255) NOT NULL,
            content TEXT NOT NULL,
            opening_hours VARCHAR(100) DEFAULT NULL,
            location VARCHAR(255) DEFAULT NULL,
            website_url VARCHAR(255) DEFAULT NULL,
            published_date DATETIME DEFAULT CURRENT_TIMESTAMP
        )
        SQL;

        self::$pdo->exec($sql);
    }

    protected function tearDown(): void
    {
        // Clear data after each test
        self::$pdo->exec('DELETE FROM advertisements');
    }

    protected function createAdvertisement(array $data = []): int
    {
        $defaults = [
            'title' => 'Test Ad',
            'content' => 'This is a test advertisement',
            'location' => 'Test Location',
            'website_url' => 'https://example.com',
            'opening_hours' => '9AM-5PM',
        ];

        $data = array_merge($defaults, $data);
        $fields = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $stmt = self::$pdo->prepare("INSERT INTO advertisements ($fields) VALUES ($placeholders)");
        $stmt->execute(array_values($data));

        return self::$pdo->lastInsertId();
    }
}
