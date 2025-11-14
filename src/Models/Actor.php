<?php
namespace App\Models;

use App\Core\Model;
use PDO;

class Actor extends Model
{
    protected $table = 'actors';

    /**
     * Crea un nuevo actor.
     * La sincronización de instituciones (M:M) debe hacerse por separado.
     */
    public function createActor(array $data)
    {
        // Asegurar que user_id sea null si está vacío
        if (empty($data['user_id'])) {
            $data['user_id'] = null;
        }

        // Eliminar datos M:M para que el create() base funcione
        unset($data['institution_id']); // Obsoleto
        unset($data['institution_ids']); // Se maneja con syncInstitutions

        return $this->create($data); // Asumiendo que create() existe en Model
    }

    /**
     * Actualiza un actor.
     * La sincronización de instituciones (M:M) debe hacerse por separado.
     */
    public function updateActor(int $id, array $data)
    {
        // Asegurar que user_id sea null si está vacío
        if (empty($data['user_id'])) {
            $data['user_id'] = null;
        }
        
        // Eliminar datos M:M para que el update() base funcione
        unset($data['institution_id']); // Obsoleto
        unset($data['institution_ids']); // Se maneja con syncInstitutions

        return $this->update($id, $data);
    }


    /**
     * MODIFICADO: Busca todos los actores y concatena sus instituciones (M:M).
     */
    public function findAllWithRelations()
    {
        $sql = "
            SELECT 
                a.*, 
                u.username AS user_name,
                GROUP_CONCAT(i.name SEPARATOR ', ') AS institution_names
            FROM 
                actors a
            LEFT JOIN 
                users u ON a.user_id = u.id
            LEFT JOIN 
                actor_institution ai ON a.id = ai.actor_id
            LEFT JOIN 
                institutions i ON ai.institution_id = i.id
            GROUP BY 
                a.id, u.username
            ORDER BY 
                a.full_name ASC
        ";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * MODIFICADO: Busca actores por rol y concatena sus instituciones (M:M).
     */
    public function findByRole(string $role)
    {
        $sql = "
            SELECT 
                a.*, 
                u.username AS user_name,
                GROUP_CONCAT(i.name SEPARATOR ', ') AS institution_names
            FROM 
                actors a
            LEFT JOIN 
                users u ON a.user_id = u.id
            LEFT JOIN 
                actor_institution ai ON a.id = ai.actor_id
            LEFT JOIN 
                institutions i ON ai.institution_id = i.id
            WHERE 
                a.role = ? AND a.active = 1
            GROUP BY 
                a.id, u.username
            ORDER BY 
                a.full_name ASC
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$role]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * MODIFICADO: Busca un actor por ID (sin relaciones de institución).
     * La lista de instituciones se debe buscar con getInstitutionsForActor().
     */
    public function findByIdWithRelations(int $id)
    {
        $sql = "
            SELECT a.*, u.username AS user_name
            FROM actors a
            LEFT JOIN users u ON a.user_id = u.id
            WHERE a.id = ?
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    /**
     * SIN CAMBIOS: Cuenta actores por rol.
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

    // --- NUEVOS MÉTODOS M:M ---

    /**
     * NUEVO: Obtener las instituciones asociadas a un actor.
     * (Para vistas de Detalle o Formulario).
     */
    public function getInstitutionsForActor(int $actorId)
    {
        $sql = "
            SELECT i.id, i.name
            FROM institutions i
            JOIN actor_institution ai ON i.id = ai.institution_id
            WHERE ai.actor_id = ?
            ORDER BY i.name ASC
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$actorId]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * NUEVO: Sincroniza las instituciones para un actor en la tabla M:M.
     * (Lo usará el controlador al guardar).
     */
    public function syncInstitutions(int $actorId, array $institutionIds)
    {
        try {
            $this->pdo->beginTransaction();

            // 1. Eliminar todas las asociaciones actuales del actor
            $sqlDelete = "DELETE FROM actor_institution WHERE actor_id = ?";
            $stmtDelete = $this->pdo->prepare($sqlDelete);
            $stmtDelete->execute([$actorId]);

            // 2. Insertar las nuevas asociaciones (si las hay)
            if (!empty($institutionIds)) {
                // Limpiar IDs (asegurarse de que sean numéricos)
                $institutionIds = array_filter($institutionIds, 'is_numeric');

                if (!empty($institutionIds)) {
                    $sqlInsert = "INSERT INTO actor_institution (actor_id, institution_id) VALUES ";
                    $values = [];
                    $placeholders = [];
                    foreach ($institutionIds as $instId) {
                        $placeholders[] = '(?, ?)';
                        $values[] = $actorId;
                        $values[] = (int)$instId;
                    }
                    $sqlInsert .= implode(', ', $placeholders);
                    
                    $stmtInsert = $this->pdo->prepare($sqlInsert);
                    $stmtInsert->execute($values);
                }
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