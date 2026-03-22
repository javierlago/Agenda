<?php

namespace App\Controllers;

use App\Models\User;
use App\Database\Database;
use PDO;

class AuthController
{
    private $userModel;
    private PDO $db;
    public function __construct()
    {
        $this->userModel = new User();
        $this->db = Database::getConnection();
    }
    /**
     * 
     * 
     * Method to handle the display of the login form and process login submissions.
     */
    public function login(): void
    {
        $error = null;

        // Si es POST, el usuario envió el formulario
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            // 1. Buscamos al usuario directamente aquí
            $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // 2. Verificamos la contraseña
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['name'];

                header("Location: index.php?action=home");
                exit;
            } else {
                $error = "Credenciales incorrectas.";
            }
        }

        // 3. Cargamos la vista (esta variable $error se usará en el HTML)
        require_once __DIR__ . '/../../views/auth/login.php';
    }
    public function logout(): void
    {
        session_destroy();
        header("Location: index.php?action=login");
        exit;
    }
}
