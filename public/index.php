<?php
// public/index.php
// php -S localhost:8000 -t public
require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Aquí más adelante configuraremos el Router
echo "<h1>Agenda App is Running</h1>";