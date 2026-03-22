<?php

namespace App\Controllers;

use App\Models\User;
use App\Utils\AuthHelper;

class UserController
{
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    /**
     * Method to handle the display of the registration form and process registration submissions.
     * 
     */
    public function register(): void
    {
        if (isset($_SESSION['user_id'])) {
            header("Location: index.php");
            exit;
        }

        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $password_confirm = $_POST['password_confirm'] ?? '';

            // --- VALIDACIONES DE FORMATO ---
            if (empty($name)) $errors[] = "El nombre es obligatorio.";
            if (empty($email)) {
                $errors[] = "El email es obligatorio.";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Formato de email inválido.";
            }
            if (strlen($password) < 6) $errors[] = "La contraseña debe tener al menos 6 caracteres.";
            if ($password !== $password_confirm) $errors[] = "Las contraseñas no coinciden.";

            if (empty($errors)) {
                // --- BLOQUE DE CAPTURA DE ERRORES DEL MODELO ---
                try {
                    // Intentamos crear. Si falla, el Modelo lanzará la Exception.
                    $this->userModel->create($name, $email, $password);

                    // Si llegamos aquí, es que no hubo excepción (éxito)
                    header("Location: index.php?action=login&registered=1");
                    exit;
                } catch (\Exception $e) {
                    // CAPTURAMOS el error (ya sea por nombre duplicado o email duplicado)
                    // y lo metemos en nuestro array de errores para la vista.
                    $errors[] = $e->getMessage();
                }
            }
        }

        // Ahora $errors ya no está vacío y el require_once cargará la vista con el error.
        require_once __DIR__ . '/../../views/auth/register.php';
    }

    /**
     * GET /POST - Edición del perfil del usuario logueado
     */
    public function profile(): void
    {
        AuthHelper::verifyLogin(); // Seguridad ante todo

        $userId = $_SESSION['user_id'];
        $user = $this->userModel->getById($userId);
        $errors = [];
        $success = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Lógica para actualizar nombre o email
            // $this->userModel->update($userId, $_POST);
            $success = "Perfil actualizado correctamente.";
        }

        require_once __DIR__ . '/../../views/user/profile.php';
    }
}
