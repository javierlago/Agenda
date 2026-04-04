<?php

namespace App\Utils;

/**
 * Provides CSRF (Cross-Site Request Forgery) protection for forms.
 * Generates a cryptographically secure token stored in the session and
 * validates submitted tokens using a timing-safe comparison.
 */
class Csrf
{
    /**
     * Returns the current session CSRF token, generating a new one if none exists.
     * The token is a 64-character hex string derived from 32 random bytes.
     *
     * @return string The CSRF token to embed in forms as a hidden field.
     */
    public static function generateToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Validates a CSRF token submitted with a form against the session token.
     * Uses hash_equals() to prevent timing-based attacks.
     *
     * @param string $token The token value from $_POST['csrf_token'].
     * @return bool True if the token matches the session token, false otherwise.
     */
    public static function validateToken(string $token): bool
    {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
}
