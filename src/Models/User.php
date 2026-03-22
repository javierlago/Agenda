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
            return false; // The email is already registered, return false to indicate failure
        }
        // Hash the password before storing it
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $sql = "INSERT INTO users (username, email, password) VALUES (:name, :email, :password)";
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
            $user = $stmt->fetch(PDO::FETCH_ASSOC); // Usamos FETCH_ASSOC para mayor claridad

            // Si no hay nada, $user será false. Devolvemos explícitamente el resultado.
            return $user ? $user : null;
        } catch (\PDOException $e) {
            error_log("Error en findByEmail: " . $e->getMessage());
            return null;
        }
    }
}
