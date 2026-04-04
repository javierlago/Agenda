<?php

namespace App\Utils;

use App\Database\Database;
use PDO;

/**
 * Enforces brute-force protection on login by tracking failed attempts per IP and email.
 * An account is locked when either the IP or the email exceeds MAX_ATTEMPTS failures
 * within a DECAY_MINUTES sliding window. Attempts are stored in the login_attempts table.
 */
class RateLimiter
{
    private const MAX_ATTEMPTS  = 5;
    private const DECAY_MINUTES = 15;

    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Checks whether the given IP or email has exceeded the failed attempt limit
     * within the current time window.
     *
     * @param string $ip    The client's IP address.
     * @param string $email The email address used in the login attempt.
     * @return bool True if the limit has been reached and the request should be blocked.
     */
    public function tooManyAttempts(string $ip, string $email): bool
    {
        $since = date('Y-m-d H:i:s', strtotime('-' . self::DECAY_MINUTES . ' minutes'));

        $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM login_attempts
            WHERE (ip = ? OR email = ?)
            AND attempted_at >= ?
        ");
        $stmt->execute([$ip, $email, $since]);

        return (int) $stmt->fetchColumn() >= self::MAX_ATTEMPTS;
    }

    /**
     * Records a single failed login attempt for the given IP and email.
     *
     * @param string $ip    The client's IP address.
     * @param string $email The email address used in the login attempt.
     * @return void
     */
    public function recordAttempt(string $ip, string $email): void
    {
        $stmt = $this->db->prepare("
            INSERT INTO login_attempts (ip, email) VALUES (?, ?)
        ");
        $stmt->execute([$ip, $email]);
    }

    /**
     * Removes all recorded attempts for the given IP and email upon a successful login.
     *
     * @param string $ip    The client's IP address.
     * @param string $email The email address used in the login attempt.
     * @return void
     */
    public function clearAttempts(string $ip, string $email): void
    {
        $stmt = $this->db->prepare("
            DELETE FROM login_attempts WHERE ip = ? OR email = ?
        ");
        $stmt->execute([$ip, $email]);
    }

    /**
     * Calculates how many minutes remain until the lockout window expires.
     * Based on the oldest attempt within the current window.
     *
     * @param string $ip    The client's IP address.
     * @param string $email The email address used in the login attempt.
     * @return int Minutes remaining until the account is unlocked (0 if already unlocked).
     */
    public function minutesUntilUnlocked(string $ip, string $email): int
    {
        $since = date('Y-m-d H:i:s', strtotime('-' . self::DECAY_MINUTES . ' minutes'));

        $stmt = $this->db->prepare("
            SELECT attempted_at FROM login_attempts
            WHERE (ip = ? OR email = ?)
            AND attempted_at >= ?
            ORDER BY attempted_at ASC
            LIMIT 1
        ");
        $stmt->execute([$ip, $email, $since]);
        $oldest = $stmt->fetchColumn();

        if (!$oldest) return 0;

        $unlocksAt = strtotime($oldest) + (self::DECAY_MINUTES * 60);
        return (int) ceil(($unlocksAt - time()) / 60);
    }
}
