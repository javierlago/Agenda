<?php

namespace App\Controllers;

use App\Models\User;
use App\Utils\AuthHelper;
use App\Utils\Logger;
use App\Utils\View;

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
                try {
                    $this->userModel->create($name, $email, $password);

                    header("Location: index.php?action=login&registered=1");
                    exit;
                } catch (\Exception $e) {
                    $errors[] = $e->getMessage();
                }
            }
        }
        View::render('auth/register', [
            'pageTitle' => 'Crear Cuenta - Agenda Pro',
            'errors'    => $errors,
        ]);
    }

    /**
     * GET /POST - Edit profile (nombre y email). Only for logged-in users.
     */
    public function profile(): void
    {
        AuthHelper::verifyLogin();

        $userId = $_SESSION['user_id'];
        $user = $this->userModel->getById($userId);
        $errors = [];
        $success = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

          
            if ($this->userModel->update($userId, $_POST)) {
                $success = "Perfil actualizado correctamente.";
           
                $user = $this->userModel->getById($userId);
            }else {
                $errors[] = "Error al actualizar el perfil. Intenta de nuevo.";
            }
        } else {
          
            $user = $this->userModel->getById($userId);
            if (!$user) {
                $errors[] = "Usuario no encontrado.";
                View::render('user/profile', [
                    'pageTitle' => 'Mi Perfil - Agenda Pro',
                    'user'    => $user,
                    'errors'  => $errors,
                    'success' => $success,
                ]);
                return;
            }
        }

        View::render('user/profile', [
            'pageTitle' => 'Mi Perfil - Agenda Pro',
            'user'    => $user,
            'errors'  => $errors,
            'success' => $success,
        ]);
    }
}
