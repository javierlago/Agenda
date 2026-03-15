<?php

namespace App\Controllers;

use App\Models\User;

class AuthController
{
    private $userModel;
    public function __construct()
    {
        $this->userModel = new User();
    }
    public function register(array $data)
    {
        $errors = [];
        // Basic validation
        $name = trim($data['name'] ?? '');
        $email = trim($data['email'] ?? '');
        $password = trim($data['password'] ?? '');
        if (empty($name)) {
            $errors[] = "Name is required.";
        }
        if (empty($email)) {
            $errors[] = "Email is required.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format.";
        }
        if (strlen($password) < 6) {
            $errors[] = "Password must be at least 6 characters.";
        }
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }
        $result = $this->userModel->create($name, $email, $password);
        if ($result) {
            return ['success' => true, 'message' => 'User registered successfully.'];
        } else {
            return ['success' => false, 'errors' => ['Failed to register user. Email may already be in use.']];
        }
    }


    public function login(array $data)
    {
        $email = trim($data['email'] ?? '');
        $password = $data['password'] ?? '';

        // 1. Validate input
        if (empty($email) || empty($password)) {
            return [
                'success' => false,
                'errors' => ["Email y contraseña son obligatorios."]
            ];
        }

        // 2. Find user by email
        $user = $this->userModel->findByEmail($email);

        // 3. Verify password and start session
        if ($user && password_verify($password, $user['password'])) {
            // Iniciamos sesión (importante: session_start() suele ir en el index, 
            // pero para esta prueba lo ponemos aquí si no ha iniciado)
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['username'];

            return [
                'success' => true,
                'message' => "Bienvenido de nuevo, " . $user['username']
            ];
        }

        return [
            'success' => false,
            'errors' => ["Credenciales incorrectas."]
        ];
    }
    public function logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        // Emppty the session array
        $_SESSION = [];
        // Droy the session cookie in the browser
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        // Finally, destroy the session in the server
        session_destroy();
        // Return a response or redirect as needed
        return [
            'success' => true,
            'message' => "Sesión cerrada correctamente."
        ];
    }
}
