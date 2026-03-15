<?php

namespace App\Controllers;

use App\Models\User;

class AuthController
{
    private $userModel;
    public function __construct()
    {
        $this->userModel = new User();
    }
    public function register(array $data)
    {
        $errors = [];
        // Basic validation
        $name = trim($data['name'] ?? '');
        $email = trim($data['email'] ?? '');
        $password = trim($data['password'] ?? '');
        if (empty($name)) {
            $errors[] = "Name is required.";
        }
        if (empty($email)) {
            $errors[] = "Email is required.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format.";
        }
        if (strlen($password) < 6) {
            $errors[] = "Password must be at least 6 characters.";
        }
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }
        $result = $this->userModel->create($name, $email, $password);
        if ($result) {
            return ['success' => true, 'message' => 'User registered successfully.'];
        } else {
            return ['success' => false, 'errors' => ['Failed to register user. Email may already be in use.']];
        }
    }
}
