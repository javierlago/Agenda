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
     * Method to handle the display of the registration form and process registration submissions.
     * 
     */
    public function register(): void
    {
        // 1. Si el usuario ya está logueado, no debería estar aquí
        if (isset($_SESSION['user_id'])) {
            header("Location: index.php");
            exit;
        }

        $errors = [];

        // 2. ¿Se ha enviado el formulario? (POST)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Recogemos los datos directamente de $_POST
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $password_confirm = $_POST['password_confirm'] ?? '';

            // --- VALIDACIONES ---
            if (empty($name)) {
                $errors[] = "El nombre es obligatorio.";
            }
            if (empty($email)) {
                $errors[] = "El email es obligatorio.";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Formato de email inválido.";
            }
            if (strlen($password) < 6) {
                $errors[] = "La contraseña debe tener al menos 6 caracteres.";
            }
            if ($password !== $password_confirm) {
                $errors[] = "Las contraseñas no coinciden.";
            }

            if (empty($errors)) {
                $result = $this->userModel->create($name, $email, $password);

                if ($result) {
                    header("Location: index.php?action=login&registered=1");
                    exit;
                } else {
                    $errors[] = "Error al registrar. El email podría estar ya en uso.";
                }
            }
        }   
        require_once __DIR__ . '/../../views/auth/register.php';
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
