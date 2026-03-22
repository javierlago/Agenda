<?php
/**
 * ARCHIVO: src/routes.php
 * Definición de rutas: 'accion' => [Controlador, Método, RequiereLogin]
 */

return [
    // Auth
    'login'          => ['AuthController', 'login', false],
    'logout'         => ['AuthController', 'logout', true],
    // Rutas de Entidad Usuario (UserController)
    'register' => ['UserController', 'register', false],
    'profile'  => ['UserController', 'profile', true],
    // Contacts
    'home'           => ['ContactController', 'index', true],
    'add_contact'    => ['ContactController', 'create', true],
    'edit_contact'   => ['ContactController', 'edit', true],
    'delete_contact' => ['ContactController', 'destroy', true],
];