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
        return $this->create($data);
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
     * Buscar institución por ID
     */
    public function findById(int $id)
    {
        return parent::findById($id);
    }
public function getById($id) {
    $sql = "SELECT * FROM {$this->table} WHERE id = ?";
    $stmt = $this->db->prepare($sql);
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
            // Error de integridad referencial
            return false;
        }
    }
}
