<?php
namespace App\Models;

use App\Core\Model;
use PDO;

class Reception extends Model
{
    protected $table = 'receptions';

    /**
     * Crear una nueva recepción
     */
    public function createReception(array $data)
    {
        return $this->create($data);
    }

    /**
     * Obtener todas las recepciones con datos de inspectores y voceros
     */
    public function findAllWithActors()
    {
        $sql = "
            SELECT r.*, 
                i.full_name AS inspector_name, 
                p.full_name AS vocero_parroquial_name
            FROM receptions r
            INNER JOIN actors i ON r.inspector_id = i.id
            INNER JOIN actors p ON r.vocero_parroquial_id = p.id
            ORDER BY r.date DESC
        ";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Buscar una recepción por ID
     */
    public function findByIdWithActors(int $id)
    {
        $sql = "
            SELECT r.*,
            i.full_name AS inspector_name,
            p.full_name AS vocero_parroquial_name
                FROM receptions r
                INNER JOIN actors i ON r.inspector_id = i.id
                INNER JOIN actors p ON r.vocero_parroquial_id = p.id
                WHERE r.id = ?
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }
}
