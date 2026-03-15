<?php

namespace App\Database;

use PDO;
use PDOException;

/**
 * 
 * Database class for managing database connections and operations.º
 */
class Database
{

    private static $instance = null;
    private $connection;

    private function __construct()
    {
        // Load database configuration from environment variables
        $host = $_ENV['DB_HOST'] ?? 'localhost';
        $db   = $_ENV['DB_NAME'] ?? '';
        $user = $_ENV['DB_USER'] ?? '';
        $pass = $_ENV['DB_PASS'] ?? '';
        $charset = $_ENV['DB_CHARSET'] ?? 'utf8mb4';

        $dns = "mysql:host=$host;dbname=$db;charset=$charset";

        // Set PDO options
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Enable exceptions for errors
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Set default fetch mode to associative array
            PDO::ATTR_EMULATE_PREPARES => false, // Disable emulation of prepared statements for better security
        ];
        try {
            // Create a new PDO instance
            $this->connection = new PDO($dns, $user, $pass, $options);
        } catch (PDOException $e) {
            // Handle connection errors
            throw new PDOException($e->getMessage(), (int)$e->getCode());
        }
    }
    // Method to get the PDO connection instance
    public static function getConnection() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance->connection;
    }

    // Prevent cloning of the singleton instance
    private function __clone() {}
}
