<?php

namespace App\Utils;

use App\Database\Database;
use PDO;

class RateLimiter
{
    private const MAX_ATTEMPTS    = 5;
    private const DECAY_MINUTES   = 15;

    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Comprueba si la IP o el email han superado el límite de intentos.
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
     * Registra un intento fallido.
     */
    public function recordAttempt(string $ip, string $email): void
    {
        $stmt = $this->db->prepare("
            INSERT INTO login_attempts (ip, email) VALUES (?, ?)
        ");
        $stmt->execute([$ip, $email]);
    }

    /**
     * Borra los intentos al hacer login correcto.
     */
    public function clearAttempts(string $ip, string $email): void
    {
        $stmt = $this->db->prepare("
            DELETE FROM login_attempts WHERE ip = ? OR email = ?
        ");
        $stmt->execute([$ip, $email]);
    }

    /**
     * Minutos que quedan hasta que se levanta el bloqueo.
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
