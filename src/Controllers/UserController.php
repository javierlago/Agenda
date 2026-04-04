<?php

namespace App\Controllers;

use App\Models\User;
use App\Utils\AuthHelper;
use App\Utils\Csrf;
use App\Utils\View;

/**
 * Handles user account actions: registration and profile management.
 */
class UserController
{
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    /**
     * Displays the registration form (GET) and processes new account submissions (POST).
     *
     * Validates the CSRF token, then checks that all fields are present and valid
     * (non-empty name, valid email format, password length >= 6, passwords match).
     * On success, creates the user and redirects to the login page. On failure,
     * re-renders the form with accumulated error messages.
     *
     * @return void
     */
    public function register(): void
    {
        if (isset($_SESSION['user_id'])) {
            header("Location: index.php");
            exit;
        }

        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Csrf::validateToken($_POST['csrf_token'] ?? '')) {
                $errors[] = "Token de seguridad inválido. Intenta de nuevo.";
            } else {
                $name             = trim($_POST['name'] ?? '');
                $email            = trim($_POST['email'] ?? '');
                $password         = $_POST['password'] ?? '';
                $password_confirm = $_POST['password_confirm'] ?? '';

                if (empty($name))   $errors[] = "El nombre es obligatorio.";
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
        }

        View::render('auth/register', [
            'pageTitle' => 'Crear Cuenta - Agenda Pro',
            'errors'    => $errors,
            'csrfToken' => Csrf::generateToken(),
        ]);
    }

    /**
     * Displays and processes the user profile page (GET/POST). Requires authentication.
     *
     * Handles two separate forms submitted to the same endpoint, distinguished by the
     * hidden 'form_type' field:
     * - 'profile': updates the display name and email via User::update().
     * - 'password': changes the password via User::changePassword() after verifying
     *   the current one.
     *
     * Both forms are protected by CSRF validation.
     *
     * @return void
     */
    public function profile(): void
    {
        AuthHelper::verifyLogin();

        $userId = $_SESSION['user_id'];
        $user   = $this->userModel->getById($userId);
        $errors = [];
        $success = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Csrf::validateToken($_POST['csrf_token'] ?? '')) {
                $errors[] = "Token de seguridad inválido. Intenta de nuevo.";
            } elseif (($_POST['form_type'] ?? '') === 'password') {
                $currentPassword = $_POST['current_password'] ?? '';
                $newPassword     = $_POST['new_password'] ?? '';
                $confirmPassword = $_POST['confirm_password'] ?? '';

                if (strlen($newPassword) < 6) {
                    $errors[] = "La nueva contraseña debe tener al menos 6 caracteres.";
                } elseif ($newPassword !== $confirmPassword) {
                    $errors[] = "Las contraseñas nuevas no coinciden.";
                } else {
                    try {
                        $this->userModel->changePassword($userId, $currentPassword, $newPassword);
                        $success = "Contraseña actualizada correctamente.";
                    } catch (\Exception $e) {
                        $errors[] = $e->getMessage();
                    }
                }
            } elseif ($this->userModel->update($userId, $_POST)) {
                $success = "Perfil actualizado correctamente.";
                $user = $this->userModel->getById($userId);
            } else {
                $errors[] = "Error al actualizar el perfil. Intenta de nuevo.";
            }
        } else {
            $user = $this->userModel->getById($userId);
            if (!$user) {
                $errors[] = "Usuario no encontrado.";
            }
        }

        View::render('user/profile', [
            'pageTitle' => 'Mi Perfil - Agenda Pro',
            'user'      => $user,
            'errors'    => $errors,
            'success'   => $success,
            'csrfToken' => Csrf::generateToken(),
        ]);
    }
}
