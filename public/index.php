<?php
// public/index.php

require_once __DIR__ . '/../vendor/autoload.php';

// Esto carga las variables del archivo .env a $_ENV
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Ahora ya puedes llamar a tu clase Database
use App\Database\Database;

try {
    $db = Database::getConnection();
    echo "Conexión exitosa a la base de datos profesional.";
} catch (\Exception $e) {
    echo "Error de conexión: " . $e->getMessage();
}