<?php
namespace App\Models;

use App\Core\Model;
use PDO;

class User extends Model {
    protected $table = 'users';

    /**
     * Crea un nuevo user con la contraseña hasheada.
     */
    public function createUser(string $username, string $password, string $role) {
        // 1. Hashear la contraseña
        // PASSWORD_DEFAULT es el algoritmo más fuerte que tu PHP soporta (actualmente bcrypt)
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        try {
            $sql = "INSERT INTO {$this->table} (username, password, role) 
                    VALUES (?, ?, ?)";
            
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$username, $hashedPassword, $role]);

        } catch (\PDOException $e) {
            // Manejar error de 'username' duplicado (SQLSTATE[23000])
            if ($e->getCode() == 23000) {
                return false; // Indica que el user ya existe
            }
            throw $e; // Lanza otras excepciones
        }
    }

    /**
     * Busca un user por su nombre user.
     */
    public function findByUsername(string $username) {
        $sql = "SELECT * FROM {$this->table} WHERE username = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$username]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    /**
     * Busca todos los users con role 'inspector'.
     */
public function findAllTechnicians() {
    $sql = "SELECT 
                u.id AS user_id,
                u.username,
                e.id AS actor_id,
                e.nombre,
                e.apellido
            FROM users u
            INNER JOIN actors e ON e.user_id = u.id
            WHERE u.role = 'inspector'
            ORDER BY u.username ASC";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_OBJ);
}


    /**
     * Busca users que NO tienen un actor asignado (user_id IS NULL en la tabla actors).
     * Incluye el user_id del actor que se está editando (si aplica).
     */
    public function findUnassignedUsers(int $current_user_id = null) {
        // Selecciona todos los users
        $sql = "
            SELECT 
                u.id, 
                u.username, 
                u.role
            FROM users u
            LEFT JOIN actors e ON u.id = e.user_id
            WHERE e.user_id IS NULL 
            OR u.id = ? 
            ORDER BY u.username ASC
        ";
        
        $stmt = $this->pdo->prepare($sql);
        // Usamos $current_user_id para asegurarnos de que el user actualmente vinculado al actor
        // (si estamos editando) aparezca como opción. Si no estamos editando, se usa NULL o 0.
        $stmt->execute([$current_user_id ?: 0]); 
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
}