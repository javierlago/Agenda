<?php

/**
 * ARCHIVO: public/index.php
 * Punto de entrada único (Front Controller)
 */

// 1. Configuración de sesión y errores
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 2. Carga de dependencias y variables de entorno
require_once __DIR__ . '/../vendor/autoload.php';
// session_destroy(); // ¡CUIDADO! Esto cerrará la sesión cada vez que cargues la página. Solo úsalo para pruebas, no lo dejes en producción.
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

use App\Controllers\AuthController;

// 3. Inicialización de controladores
$auth = new AuthController();

// 4. Captura de la acción (por defecto 'home')
$action = $_GET['action'] ?? 'home';

/**
 * 5. CONTROLADOR DE RUTAS (Switch)
 * Aquí decidimos qué lógica ejecutar y qué vista cargar
 */
switch ($action) {
    case 'login':
        // Si el usuario ya está logueado, lo mandamos al home
        if (isset($_SESSION['user_id'])) {
            header("Location: index.php");
            exit;
        }

        // Si se envía el formulario por POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $response = $auth->login($_POST);

            if ($response['success']) {
                header("Location: index.php");
                exit;
            } else {
                // Si hay error, capturamos el mensaje para la vista
                $error = $response['errors'][0];
            }
        }
        // Cargamos la vista de login (Ruta desde public/ a views/)
        require_once __DIR__ . '/../views/auth/login.php';
        break;

    case 'logout':
        $auth->logout();
        header("Location: index.php");
        exit;

    case 'register':
        // Lógica de registro (similar al login pero con el método create)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $response = $auth->register($_POST);
            if ($response['success']) {
                header("Location: index.php?action=login&registered=1");
                exit;
            } else {
                $error = $response['errors'][0];
            }
        }
        require_once __DIR__ . '/../views/auth/register.php';
        break;

    case 'home':
    default:
        // PROTECCIÓN: Si no hay sesión, obligamos a ir al Login
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?action=login");
            exit;
        }

        // Si el usuario está logueado, mostramos su Dashboard/Agenda
        require_once __DIR__ . '/../views/contacts/index.php';
        break;
}
