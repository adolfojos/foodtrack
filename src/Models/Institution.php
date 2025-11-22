<?php
namespace App\Models;

use App\Core\Model;
use PDO;

class Institution extends Model
{
    protected $table = 'institutions';

    /**
     * Crear una nueva institución
     */
    public function createInstitution(array $data)
    {
        return $this->create($data); // Retorna el ID insertado
    }

    /**
     * Listar todas las instituciones activas
     */
    public function findAllActive()
    {
        $sql = "SELECT * FROM {$this->table} WHERE active = 1 ORDER BY name ASC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
 * Listar todas las instituciones con sus directores asociados concatenados.
 */
public function findAllWithDirectors()
{
    // GROUP_CONCAT se usa para juntar múltiples nombres de directores en una sola celda.
    $sql = "
        SELECT 
            i.*, 
            GROUP_CONCAT(a.full_name SEPARATOR ', ') AS director_names
        FROM 
            {$this->table} i
        LEFT JOIN 
            actor_institution ai ON i.id = ai.institution_id
        LEFT JOIN 
            actors a ON ai.actor_id = a.id AND a.role = 'director'
        GROUP BY 
            i.id
        ORDER BY 
            i.name ASC
    ";
    
    $stmt = $this->pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_OBJ);
}
    /**
     * Buscar institución por ID
     */
    public function findById(int $id)
    {
        return parent::findById($id);
    }
    
    public function getById($id) 
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    /**
     * Actualizar institución
     */
    public function updateInstitution(int $id, array $data)
    {
        return $this->update($id, $data);
    }

    /**
     * Desactivar institución (soft block)
     */
    public function deactivate(int $id)
    {
        $sql = "UPDATE {$this->table} SET active = 0 WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }

    /**
     * Eliminar una institución
     */
    public function delete(int $id): bool
    {
        try {
            $sql = "DELETE FROM {$this->table} WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$id]);
        } catch (\PDOException $e) {
            // Error de integridad referencial. Registrar el error si es necesario.
            return false;
        }
    }

    // --- NUEVOS MÉTODOS M:M ---

    /**
     * Obtener los directores asociados a esta institución (Relación M:M)
     */
    public function getDirectors(int $institutionId)
    {
        $sql = "
            SELECT 
                a.id, 
                a.full_name, 
                a.national_id 
            FROM 
                actors a
            JOIN 
                actor_institution ai ON a.id = ai.actor_id
            WHERE 
                ai.institution_id = :institution_id 
                AND a.role = 'director'
            ORDER BY a.full_name ASC
        ";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':institution_id', $institutionId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Obtener todos los actores que tienen un rol específico para selección en formularios.
     */
    public function getAllActorsByRoleForSelection(string $role)
    {
        $sql = "SELECT id, full_name FROM actors WHERE role = ? AND active = TRUE ORDER BY full_name ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$role]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
    
    /**
     * Sincroniza los actores asociados a una institución (Lógica M:M).
     * 1. Elimina asociaciones antiguas para el rol.
     * 2. Inserta nuevas asociaciones.
     */
    public function syncActors(int $institutionId, array $actorIds, string $role)
    {
        // 1. Validar que los IDs pertenezcan al rol
        $validActorIds = [];
        if (!empty($actorIds)) {
            $inClause = implode(',', array_fill(0, count($actorIds), '?'));
            $sqlValidate = "SELECT id FROM actors WHERE id IN ({$inClause}) AND role = ?";
            $stmtValidate = $this->pdo->prepare($sqlValidate);
            $stmtValidate->execute(array_merge($actorIds, [$role]));
            $validActorIds = array_column($stmtValidate->fetchAll(PDO::FETCH_ASSOC), 'id');
        }

        try {
            $this->pdo->beginTransaction();

            // 1. Eliminar asociaciones de ESTE rol para la institución
            $sqlDelete = "
                DELETE ai FROM actor_institution ai
                JOIN actors a ON ai.actor_id = a.id
                WHERE ai.institution_id = ? AND a.role = ?
            ";
            $stmtDelete = $this->pdo->prepare($sqlDelete);
            $stmtDelete->execute([$institutionId, $role]);

            // 2. Insertar las nuevas asociaciones válidas
            if (!empty($validActorIds)) {
                $sqlInsert = "INSERT INTO actor_institution (institution_id, actor_id) VALUES ";
                $values = [];
                $placeholders = [];
                foreach ($validActorIds as $actorId) {
                    $placeholders[] = '(?, ?)';
                    $values[] = $institutionId;
                    $values[] = $actorId;
                }
                $sqlInsert .= implode(', ', $placeholders);
                
                $stmtInsert = $this->pdo->prepare($sqlInsert);
                $stmtInsert->execute($values);
            }

            $this->pdo->commit();
            return true;
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            // Implementa un registro de errores adecuado aquí
            return false;
        }
    }
}