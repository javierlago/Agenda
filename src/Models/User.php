<?php

namespace App\Models;

use App\Database\Database;
use PDO;
use PDOException;

class User
{
    private $db;
    private $id;
    private $name;
    private $email;
    private $password;
    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function create(string $name, string $email, string $password): bool
    {
        if ($this->findByEmail($email)) {
            return false; // El email ya está registrado
        }
        // Hash the password before storing it
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $sql = "INSERT INTO users (name, email, password) VALUES (:name, :email, :password)";
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':name' => $name,
                ':email' => $email,
                ':password' => $hashedPassword
            ]);
        } catch (PDOException $e) {
            // Handle any exceptions that occur during the database operation
            error_log("Error creatin a user: " . $e->getMessage());
            return false;
        }
    }
    public function findByEmail(string $email)
    {
        $sql = "SELECT * FROM users WHERE email = :email LIMIT 1";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':email' => $email]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error finding by Email: " . $e->getMessage());
            return false;
        }
    }
}
