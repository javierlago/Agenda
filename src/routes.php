<?php
/**
 * ARCHIVO: src/routes.php
 * Definición de rutas: 'accion' => [Controlador, Método, RequiereLogin]
 */

return [
    // Auth
    'login'          => ['AuthController', 'login', false],
    'register'       => ['AuthController', 'register', false],
    'logout'         => ['AuthController', 'logout', true],
    
    // Contacts
    'home'           => ['ContactController', 'index', true],
    'add_contact'    => ['ContactController', 'create', true],
    'edit_contact'   => ['ContactController', 'edit', true],
    'delete_contact' => ['ContactController', 'destroy', true],
];