<?php
namespace App\Models;

use App\Core\Model;
use PDO;

class User extends Model {
    
    protected $table = 'users';

    /**
     * CORREGIDO: Crea un usuario (solo login) y devuelve su ID.
     * Ya no acepta 'role', pues esa columna no existe en 'users'.
     * Devuelve el ID para que podamos enlazarlo al crear el Actor.
     */
    public function createUser(string $username, string $password): ?int
    {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        try {
            // SQL CORREGIDO: Solo inserta username y password
            $sql = "INSERT INTO {$this->table} (username, password) VALUES (?, ?)";
            $stmt = $this->pdo->prepare($sql);
            
            if ($stmt->execute([$username, $hashedPassword])) {
                // Devolvemos el ID del usuario recién creado
                return (int)$this->pdo->lastInsertId();
            }
            return null;

        } catch (\PDOException $e) {
            // El código 23000 es de violación de restricción (ej. 'username' duplicado)
            if ($e->getCode() == 23000) {
                return null;
            }
            throw $e;
        }
    }

    /**
     * ELIMINADO: El método 'create(array $data)' fue removido.
     * Era incorrecto, intentaba insertar 'email', 'role', 'active'
     * en la tabla 'users', lo cual es imposible.
     */

    /**
     * CORRECTO: Se mantiene para el proceso de Login.
     */
    public function findByUsername(string $username) {
        $sql = "SELECT * FROM {$this->table} WHERE username = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$username]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    /**
     * ELIMINADO: 'findByUsernameOrEmail'.
     * La tabla 'users' no tiene email. La lógica de buscar por email
     * debe residir en el ActorModel.
     */

    /**
     * ELIMINADO: 'findAllTechnicians'.
     * Esta lógica le pertenece a ActorModel (y ya la tienes):
     * $actorModel->findByRole('inspector');
     */

    /**
     * CORREGIDO: 'findUnassignedUsers'.
     * Esta es una consulta útil para un panel de admin (ver logins sin perfil).
     * Se corrigió para que no intente seleccionar 'u.role' (que no existe).
     */
    public function findUnassignedUsers(int $current_actor_user_id = null) {
        // Nota: $current_actor_user_id es el ID del *usuario* del actor que
        // está siendo editado, para que aparezca en la lista él mismo.
        
        $sql = "
            SELECT u.id, u.username
            FROM users u
            LEFT JOIN actors a ON u.id = a.user_id
            WHERE a.id IS NULL OR u.id = ?
            ORDER BY u.username ASC
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$current_actor_user_id ?: 0]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * CORRECTO: Se mantiene.
     */
    public function find(int $id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    /**
     * ELIMINADO: 'countByRole'.
     * La tabla 'users' no tiene 'role'. Esta lógica le pertenece
     * al ActorModel (y ya la tienes con 'countActorsByRole').
     */



/**
 * Busca un usuario por username y obtiene su rol y datos de actor asociado.
 */
public function findUserWithActor(string $username)
{
    // Usamos INNER JOIN para asegurar que el usuario tenga un perfil de actor asociado.
    $sql = "
        SELECT 
            u.id, 
            u.username, 
            u.password, 
            a.id AS actor_id, 
            a.role, 
            a.full_name
        FROM users u
        INNER JOIN actors a ON u.id = a.user_id
        WHERE u.username = ?
    ";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$username]);
    // Devolvemos un solo objeto con todos los datos
    return $stmt->fetch(PDO::FETCH_OBJ);
}
}