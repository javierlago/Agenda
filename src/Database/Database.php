<?php

namespace App\Database;

use PDO;
use PDOException;

/**
 * Manages the PDO database connection using the Singleton pattern.
 * Ensures a single shared connection instance throughout the application lifecycle.
 */
class Database
{
    private static ?self $instance = null;
    private PDO $connection;

    /**
     * Private constructor to prevent direct instantiation.
     * Reads credentials from environment variables and creates the PDO connection.
     *
     * @throws PDOException If the connection cannot be established.
     */
    private function __construct()
    {
        $host    = $_ENV['DB_HOST']    ?? 'localhost';
        $db      = $_ENV['DB_NAME']    ?? '';
        $user    = $_ENV['DB_USER']    ?? '';
        $pass    = $_ENV['DB_PASS']    ?? '';
        $charset = $_ENV['DB_CHARSET'] ?? 'utf8mb4';

        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->connection = new PDO($dsn, $user, $pass, $options);
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage(), (int) $e->getCode());
        }
    }

    /**
     * Returns the shared PDO connection instance, creating it if necessary.
     *
     * @return PDO The active database connection.
     */
    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance->connection;
    }

    private function __clone() {}
}
