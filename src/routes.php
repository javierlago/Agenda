<?php

/**
 * Application route map.
 * Format: 'action' => [ControllerClass, method, requiresAuth]
 */
return [
    // Auth
    'login'          => ['AuthController', 'login',   false],
    'logout'         => ['AuthController', 'logout',  true],
    'register'       => ['UserController', 'register', false],

    // User
    'profile'        => ['UserController', 'profile', true],

    // Contacts
    'home'           => ['ContactController', 'index',   true],
    'add_contact'    => ['ContactController', 'create',  true],
    'edit_contact'   => ['ContactController', 'edit',    true],
    'delete_contact' => ['ContactController', 'destroy', true],
];
