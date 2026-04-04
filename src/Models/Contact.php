<?php

namespace App\Models;

use App\Database\Database;
use PDO;
use PDOException;

/**
 * Class Contact
 * Handles Database operations (CRUD) for the contacts table.
 */
class Contact
{
    /** @var PDO $db Database connection instance */
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Creates a new contact in the database.
     *
     * @param array $data Associative array with contact details.
     * @return bool True on success, false on failure.
     */
    public function create(array $data): bool
    {
        $sql = "INSERT INTO contacts (user_id, name, phone, email, description) 
                VALUES (:user_id, :name, :phone, :email, :description)";
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':user_id'     => $data['user_id'],
                ':name'        => $data['name'],
                ':phone'       => $data['phone'] ?? null,
                ':email'       => $data['email'] ?? null,
                ':description' => $data['description'] ?? null
            ]);
        } catch (PDOException $e) {
            error_log("Error creating contact: " . $e->getMessage());
            return false;
        }
    }
    public function findById(int $id, int $userId): ?array
    {
        $sql = "SELECT * FROM contacts WHERE id = :id AND user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id, ':user_id' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
    /**
     * Updates an existing contact record.
     *
     * @param int $id The contact ID.
     * @param array $data New contact details.
     * @return bool True on success, false on failure.
     */
    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE contacts SET name = :name, phone = :phone, email = :email, description = :description
                WHERE id = :id AND user_id = :user_id";
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':id'          => $id,
                ':user_id'     => $data['user_id'],
                ':name'        => $data['name'],
                ':phone'       => $data['phone'] ?? null,
                ':email'       => $data['email'] ?? null,
                ':description' => $data['description'] ?? null
            ]);
        } catch (PDOException $e) {
            error_log("Error updating contact: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Retrieves all contacts belonging to a specific user.
     *
     * @param int $userId The ID of the logged-in user.
     * @return array List of contacts.
     */
    public function getAllByUserId(int $userId): array
    {
        $sql = "SELECT * FROM contacts WHERE user_id = :user_id ORDER BY name ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Deletes a contact record.
     *
     * @param int $id The contact ID.
     * @param int $userId Owner ID for security.
     * @return bool
     */
    public function delete(int $id, int $userId): bool
    {
        $sql = "DELETE FROM contacts WHERE id = :id AND user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id, ':user_id' => $userId]);
    }
    /**
     *  Pagination method to retrieve a subset of contacts for a user.
     * @param int $userId The ID of the logged-in user.
     * @param int $limit Number of contacts per page.
     * @param int $offset Number of contacts to skip (for pagination).
     */

    public function getPaginated(int $userId, int $limit, int $offset, string $search = '', string $sort = 'name_asc'): array
    {
        $orderBy = match($sort) {
            'name_desc' => 'name DESC',
            'date_asc'  => 'created_at ASC',
            'date_desc' => 'created_at DESC',
            default     => 'name ASC',
        };

        if ($search !== '') {
            $stmt = $this->db->prepare("
                SELECT * FROM contacts
                WHERE user_id = ?
                AND (name LIKE ? OR phone LIKE ? OR email LIKE ?)
                ORDER BY {$orderBy}
                LIMIT ? OFFSET ?
            ");
            $term = '%' . $search . '%';
            $stmt->bindValue(1, $userId, \PDO::PARAM_INT);
            $stmt->bindValue(2, $term);
            $stmt->bindValue(3, $term);
            $stmt->bindValue(4, $term);
            $stmt->bindValue(5, $limit, \PDO::PARAM_INT);
            $stmt->bindValue(6, $offset, \PDO::PARAM_INT);
        } else {
            $stmt = $this->db->prepare("
                SELECT * FROM contacts
                WHERE user_id = ?
                ORDER BY {$orderBy}
                LIMIT ? OFFSET ?
            ");
            $stmt->bindValue(1, $userId, \PDO::PARAM_INT);
            $stmt->bindValue(2, $limit, \PDO::PARAM_INT);
            $stmt->bindValue(3, $offset, \PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchAll();
    }
    /**
     * Obtiene el total de contactos para un usuario específico (útil para paginación).
     * @param int $userId
     * @return int
     */
    public function getTotalCount(int $userId, string $search = ''): int
    {
        if ($search !== '') {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) FROM contacts
                WHERE user_id = ?
                AND (name LIKE ? OR phone LIKE ? OR email LIKE ?)
            ");
            $term = '%' . $search . '%';
            $stmt->execute([$userId, $term, $term, $term]);
        } else {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM contacts WHERE user_id = ?");
            $stmt->execute([$userId]);
        }
        return (int) $stmt->fetchColumn();
    }
}
