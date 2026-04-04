<?php

namespace App\Utils;

/**
 * Provides utility methods for authentication and session management.
 */
class AuthHelper
{
    /**
     * Verifies that a user is currently logged in.
     * Starts the session if not already active, then redirects to the login
     * page and halts execution if no valid session is found.
     *
     * @return void
     */
    public static function verifyLogin(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?action=login");
            exit;
        }
    }
}
