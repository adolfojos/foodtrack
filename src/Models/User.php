<?php
namespace App\Models;

use App\Core\Model;
use PDO;

class User extends Model {
    protected $table = 'users';

    public function createUser(string $username, string $password, string $role) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        try {
            $sql = "INSERT INTO {$this->table} (username, password, role) VALUES (?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$username, $hashedPassword, $role]);
        } catch (\PDOException $e) {
            if ($e->getCode() == 23000) {
                return false;
            }
            throw $e;
        }
    }

    public function create(array $data) {
        $sql = "INSERT INTO {$this->table} (username, email, password, role, active) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $data['username'],
            $data['email'],
            $data['password'],
            $data['role'] ?? 'actor',
            $data['active'] ?? 1
        ]);
        return $this->pdo->lastInsertId();
    }

    public function findByUsername(string $username) {
        $sql = "SELECT * FROM {$this->table} WHERE username = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$username]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function findByUsernameOrEmail(string $username, string $email) {
        $sql = "SELECT * FROM {$this->table} WHERE username = ? OR email = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$username, $email]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function findAllTechnicians() {
        $sql = "SELECT u.id AS user_id, u.username, e.id AS actor_id, e.nombre, e.apellido
                FROM users u
                INNER JOIN actors e ON e.user_id = u.id
                WHERE u.role = 'inspector'
                ORDER BY u.username ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function findUnassignedUsers(int $current_user_id = null) {
        $sql = "SELECT u.id, u.username, u.role
                FROM users u
                LEFT JOIN actors e ON u.id = e.user_id
                WHERE e.user_id IS NULL OR u.id = ?
                ORDER BY u.username ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$current_user_id ?: 0]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
    /**
     * Busca un usuario por su ID.
     */
    public function find(int $id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    /**
     * Cuenta cuántos usuarios tienen un rol específico.
     */
    public function countByRole(string $role): int
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE role = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$role]);
        return (int)$stmt->fetchColumn();
    }
}
