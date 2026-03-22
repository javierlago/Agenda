<?php

namespace App\Utils;

/**
 * Class AuthHelper
 * Provides utility methods for authentication and session management.
 */
class AuthHelper
{
    /**
     * Verifies if a user is logged in. 
     * If not, redirects to the login page and stops execution.
     * * @return void
     */
    public static function verifyLogin(): void
    {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?action=login");
            exit;
        }
    }
}