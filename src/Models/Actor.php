<?php
namespace App\Models;

use App\Core\Model;
use PDO;

class Actor extends Model
{
    protected $table = 'actors';

    public function createActor(array $data)
    {
        if (empty($data['user_id'])) {
            $data['user_id'] = null;
        }
        unset($data['institution_ids']);
        return $this->create($data);
    }

    public function updateActor(int $id, array $data)
    {
        if (empty($data['user_id'])) {
            $data['user_id'] = null;
        }
        unset($data['institution_ids']);
        return $this->update($id, $data);
    }

    public function findAllWithRelations()
    {
        $sql = "
            SELECT a.*, u.username AS user_name,
                GROUP_CONCAT(DISTINCT i.name ORDER BY i.name SEPARATOR ', ') AS institution_names
            FROM actors a
            LEFT JOIN users u ON a.user_id = u.id
            LEFT JOIN actor_institution ai ON a.id = ai.actor_id
            LEFT JOIN institutions i ON ai.institution_id = i.id
            GROUP BY a.id, u.username
            ORDER BY a.full_name ASC
        ";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_OBJ);
    }

    public function findByRole(string $role)
    {
        $sql = "
            SELECT a.*, u.username AS user_name,
                GROUP_CONCAT(DISTINCT i.name ORDER BY i.name SEPARATOR ', ') AS institution_names
            FROM actors a
            LEFT JOIN users u ON a.user_id = u.id
            LEFT JOIN actor_institution ai ON a.id = ai.actor_id
            LEFT JOIN institutions i ON ai.institution_id = i.id
            WHERE a.role = ? AND a.active = 1
            GROUP BY a.id, u.username
            ORDER BY a.full_name ASC
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$role]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function findByIdWithRelations(int $id)
    {
        $sql = "SELECT a.*, u.username AS user_name
                FROM actors a
                LEFT JOIN users u ON a.user_id = u.id
                WHERE a.id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function findByNationalId(string $nationalId)
    {
        $sql = "SELECT id, full_name FROM actors WHERE national_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$nationalId]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function countActorsByRole()
    {
        $sql = "SELECT role, COUNT(id) as count 
                FROM {$this->table} 
                WHERE active = 1
                GROUP BY role";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_OBJ);
    }

    public function getInstitutionsForActor(int $actorId)
    {
        $sql = "SELECT i.id, i.name
                FROM institutions i
                JOIN actor_institution ai ON i.id = ai.institution_id
                WHERE ai.actor_id = ?
                ORDER BY i.name ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$actorId]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function syncInstitutions(int $actorId, array $institutionIds)
    {
        try {
            $this->pdo->beginTransaction();
            $this->pdo->prepare("DELETE FROM actor_institution WHERE actor_id = ?")
                    ->execute([$actorId]);

            if (!empty($institutionIds)) {
                $institutionIds = array_filter($institutionIds, 'is_numeric');
                if (!empty($institutionIds)) {
                    $sql = "INSERT INTO actor_institution (actor_id, institution_id) VALUES ";
                    $placeholders = [];
                    $values = [];
                    foreach ($institutionIds as $instId) {
                        $placeholders[] = "(?, ?)";
                        $values[] = $actorId;
                        $values[] = (int)$instId;
                    }
                    $sql .= implode(", ", $placeholders);
                    $stmt = $this->pdo->prepare($sql);
                    $stmt->execute($values);
                }
            }
            $this->pdo->commit();
            return true;
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            return false;
        }
    }
public function find(int $id)
{
    $sql = "SELECT * FROM {$this->table} WHERE id = ?";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_OBJ);
}

    //  Validación de rol único por institución
    public function existsRoleInInstitution(string $role, int $institutionId, ?int $excludeActorId = null)
    {
        $sql = "SELECT COUNT(*) as count
                FROM actor_institution ai
                JOIN actors a ON ai.actor_id = a.id
                WHERE ai.institution_id = ? AND a.role = ? AND a.active = 1";
        if ($excludeActorId) {
            $sql .= " AND a.id != ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$institutionId, $role, $excludeActorId]);
        } else {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$institutionId, $role]);
        }
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result->count > 0;
    }
}
