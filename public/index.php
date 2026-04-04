<?php

session_start();
require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

use App\Utils\AuthHelper;

$routes = require_once __DIR__ . '/../src/routes.php';

$action = $_GET['action'] ?? 'home';

if (array_key_exists($action, $routes)) {
    [$controllerName, $method, $requiresAuth] = $routes[$action];

    if ($requiresAuth) {
        AuthHelper::verifyLogin();
    }

    $controllerClass = "App\\Controllers\\" . $controllerName;
    $controller = new $controllerClass();
    $controller->$method();
} else {
    header("HTTP/1.0 404 Not Found");
    echo "404 - Page not found";
}
