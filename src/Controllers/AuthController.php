<?php

namespace App\Controllers;

use App\Models\User;
use App\Database\Database;
use App\Utils\Csrf;
use App\Utils\RateLimiter;
use App\Utils\View;
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
        $email = '';
        $rateLimiter = new RateLimiter();
        $ip = $_SERVER['REMOTE_ADDR'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email    = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            if (!Csrf::validateToken($_POST['csrf_token'] ?? '')) {
                $error = "Token de seguridad inválido. Intenta de nuevo.";
            } elseif ($rateLimiter->tooManyAttempts($ip, $email)) {
                $minutes = $rateLimiter->minutesUntilUnlocked($ip, $email);
                $error = "Demasiados intentos fallidos. Espera {$minutes} minuto(s) antes de volver a intentarlo.";
            } else {
                $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
                $stmt->execute(['email' => $email]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user && password_verify($password, $user['password'])) {
                    $rateLimiter->clearAttempts($ip, $email);
                    $_SESSION['user_id']  = $user['id'];
                    $_SESSION['username'] = $user['name'];

                    header("Location: index.php?action=home");
                    exit;
                } else {
                    $rateLimiter->recordAttempt($ip, $email);
                    $error = "Credenciales incorrectas.";
                }
            }
        }

        View::render('auth/login', ['error' => $error, 'email' => $email, 'csrfToken' => Csrf::generateToken()]);
    }
    public function logout(): void
    {
        session_destroy();
        header("Location: index.php?action=login");
        exit;
    }
}
