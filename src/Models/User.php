<?php

namespace App\Models;

use App\Database\Database;
use PDO;
use PDOException;
use App\Utils\Logger;

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
        try {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $this->db->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            return $stmt->execute([$name, $email, $hash]);
        } catch (\PDOException $e) {
            if ($e->getCode() == 23000) {
                // Analizamos el mensaje de error de MySQL para saber qué campo falló
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

    /**
     * Obtiene un usuario por su ID.
     * @param int $id
     * @return array|bool Retorna el array del usuario o false si no existe.
     */
    public function getById(int $id)
    {
        $stmt = $this->db->prepare("SELECT id, username, email, created_at FROM users WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
        // Esto es un comentario
    }
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
        Logger::log('user_updates', "sql: $sql, params: " . json_encode($params));

        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($params);
        } catch (\PDOException $e) {
            if ($e->getCode() == 23000) {
                // Analizamos el mensaje de error de MySQL para saber qué campo falló
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
}
