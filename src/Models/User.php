<?php

namespace App\Models;

use App\Database\Database;
use PDO;
use PDOException;

/**
 * Handles all database operations related to user accounts.
 */
class User
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Creates a new user account with a bcrypt-hashed password.
     * Throws a descriptive exception if the email or username is already taken.
     *
     * @param string $name     The user's display name.
     * @param string $email    The user's email address (must be unique).
     * @param string $password Plain-text password to be hashed before storing.
     * @return bool True on success.
     * @throws \Exception If the email or username already exists, or on any DB error.
     */
    public function create(string $name, string $email, string $password): bool
    {
        try {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $this->db->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            return $stmt->execute([$name, $email, $hash]);
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $errorInfo = $e->getMessage();

                if (str_contains($errorInfo, 'email')) {
                    throw new \Exception("Este correo electrónico ya está registrado.");
                }
                if (str_contains($errorInfo, 'name') || str_contains($errorInfo, 'username')) {
                    throw new \Exception("El nombre de usuario ya está en uso. Elige otro.");
                }
                throw new \Exception("Ya existe un registro con estos datos.");
            }
            throw new \Exception("Error técnico: " . $e->getMessage());
        }
    }

    /**
     * Looks up a user by their email address.
     *
     * @param string $email The email to search for.
     * @return array|null The user row as an associative array, or null if not found.
     */
    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    }

    /**
     * Retrieves a user by their primary key, excluding the password column.
     *
     * @param int $id The user's ID.
     * @return array|false The user row, or false if not found.
     */
    public function getById(int $id)
    {
        $stmt = $this->db->prepare("SELECT id, username, email, created_at FROM users WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Updates the username and/or email of an existing user.
     * Throws a descriptive exception if the new email or username is already taken.
     *
     * @param int   $id   The user's ID.
     * @param array $data Associative array that may contain 'username' and/or 'email'.
     * @return bool True on success.
     * @throws \Exception If a duplicate value is detected or no fields are provided.
     */
    public function update(int $id, array $data): bool
    {
        $fields = [];
        $params = ['id' => $id];

        if (isset($data['username'])) {
            $fields[] = "username = :username";
            $params['username'] = trim($data['username']);
        }
        if (isset($data['email'])) {
            $fields[] = "email = :email";
            $params['email'] = trim($data['email']);
        }

        if (empty($fields)) {
            throw new \Exception("No hay datos para actualizar.");
        }

        $sql = "UPDATE users SET " . implode(", ", $fields) . " WHERE id = :id";

        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $errorInfo = $e->getMessage();

                if (str_contains($errorInfo, 'email')) {
                    throw new \Exception("Este correo electrónico ya está registrado por otro usuario.");
                }
                if (str_contains($errorInfo, 'name') || str_contains($errorInfo, 'username')) {
                    throw new \Exception("El nombre de usuario ya está en uso por otro usuario. Elige otro.");
                }
                throw new \Exception("Ya existe un registro con estos datos.");
            }
            throw new \Exception("Error técnico: " . $e->getMessage());
        }
    }

    /**
     * Changes a user's password after verifying the current one.
     * Throws an exception if the current password does not match.
     *
     * @param int    $id              The user's ID.
     * @param string $currentPassword The user's existing plain-text password.
     * @param string $newPassword     The new plain-text password to hash and store.
     * @return bool True on success.
     * @throws \Exception If the current password is incorrect.
     */
    public function changePassword(int $id, string $currentPassword, string $newPassword): bool
    {
        $stmt = $this->db->prepare("SELECT password FROM users WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row || !password_verify($currentPassword, $row['password'])) {
            throw new \Exception("La contraseña actual es incorrecta.");
        }

        $hash = password_hash($newPassword, PASSWORD_BCRYPT);
        $stmt = $this->db->prepare("UPDATE users SET password = :password WHERE id = :id");
        return $stmt->execute([':password' => $hash, ':id' => $id]);
    }
}
