<?php

namespace App\Models;

use App\Database\Database;
use PDO;
use PDOException;

/**
 * Handles all database operations for the contacts table.
 * All queries are scoped to a specific user_id to enforce data isolation.
 */
class Contact
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Inserts a new contact record for the given user.
     *
     * @param array $data Must contain 'user_id' and 'name'. 'phone', 'email', and 'description' are optional.
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
                ':description' => $data['description'] ?? null,
            ]);
        } catch (PDOException $e) {
            error_log("Error creating contact: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Retrieves a single contact by ID, restricted to the given user.
     * Returns null if the contact does not exist or belongs to a different user.
     *
     * @param int $id     The contact's primary key.
     * @param int $userId The ID of the logged-in user (ownership check).
     * @return array|null The contact row, or null if not found.
     */
    public function findById(int $id, int $userId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM contacts WHERE id = :id AND user_id = :user_id");
        $stmt->execute([':id' => $id, ':user_id' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Updates all fields of an existing contact.
     * The user_id check ensures a user cannot edit another user's contacts.
     *
     * @param int   $id   The contact's primary key.
     * @param array $data New values. Must contain 'user_id', 'name'. 'phone', 'email', 'description' are optional.
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
                ':description' => $data['description'] ?? null,
            ]);
        } catch (PDOException $e) {
            error_log("Error updating contact: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Deletes a contact record, restricted to the given user for security.
     *
     * @param int $id     The contact's primary key.
     * @param int $userId The ID of the logged-in user (ownership check).
     * @return bool True if a row was deleted, false otherwise.
     */
    public function delete(int $id, int $userId): bool
    {
        $stmt = $this->db->prepare("DELETE FROM contacts WHERE id = :id AND user_id = :user_id");
        return $stmt->execute([':id' => $id, ':user_id' => $userId]);
    }

    /**
     * Returns a paginated, optionally filtered and sorted list of contacts for a user.
     *
     * @param int    $userId The ID of the logged-in user.
     * @param int    $limit  Number of records per page.
     * @param int    $offset Number of records to skip (calculated as ($page - 1) * $limit).
     * @param string $search Optional search term matched against name, phone, and email.
     * @param string $sort   Sort key: 'name_asc' (default), 'name_desc', 'date_asc', 'date_desc'.
     * @return array List of contact rows as associative arrays.
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
            $stmt->bindValue(1, $userId, PDO::PARAM_INT);
            $stmt->bindValue(2, $term);
            $stmt->bindValue(3, $term);
            $stmt->bindValue(4, $term);
            $stmt->bindValue(5, $limit, PDO::PARAM_INT);
            $stmt->bindValue(6, $offset, PDO::PARAM_INT);
        } else {
            $stmt = $this->db->prepare("
                SELECT * FROM contacts
                WHERE user_id = ?
                ORDER BY {$orderBy}
                LIMIT ? OFFSET ?
            ");
            $stmt->bindValue(1, $userId, PDO::PARAM_INT);
            $stmt->bindValue(2, $limit, PDO::PARAM_INT);
            $stmt->bindValue(3, $offset, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Returns the total number of contacts for a user, respecting the active search filter.
     * Used to calculate the total number of pages for pagination.
     *
     * @param int    $userId The ID of the logged-in user.
     * @param string $search Optional search term applied to name, phone, and email.
     * @return int Total matching contact count.
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
