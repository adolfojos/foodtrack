<?php
namespace App\Models;

use App\Core\Model;
use PDO;

class Actor extends Model
{
    protected $table = 'actors';

    public function createActor(array $data)
    {
        // Asegurar que user_id sea null si está vacío
        if (empty($data['user_id'])) {
            $data['user_id'] = null;
        }

        return $this->create($data); // Asumiendo que create() existe en Model
    }

    public function findAllWithRelations()
    {
        $sql = "
            SELECT a.*, i.name AS institution_name, u.username AS user_name
            FROM actors a
            LEFT JOIN institutions i ON a.institution_id = i.id
            LEFT JOIN users u ON a.user_id = u.id
            ORDER BY a.full_name ASC
        ";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function findByRole(string $role)
    {
        $sql = "
            SELECT a.*, i.name AS institution_name, u.username AS user_name
            FROM actors a
            LEFT JOIN institutions i ON a.institution_id = i.id
            LEFT JOIN users u ON a.user_id = u.id
            WHERE a.role = ? AND a.active = 1
            ORDER BY a.full_name ASC
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$role]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function findByIdWithRelations(int $id)
    {
        $sql = "
            SELECT a.*, i.name AS institution_name, u.username AS user_name
            FROM actors a
            LEFT JOIN institutions i ON a.institution_id = i.id
            LEFT JOIN users u ON a.user_id = u.id
            WHERE a.id = ?
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }
    /**
 * Cuenta el número de actores activos agrupados por su rol.
 * @return array Una lista de objetos con 'role' y 'count'.
 */
public function countActorsByRole()
{
    $sql = "
        SELECT role, COUNT(id) as count 
        FROM {$this->table} 
        WHERE active = 1
        GROUP BY role
    ";
    $stmt = $this->pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_OBJ);
}
}