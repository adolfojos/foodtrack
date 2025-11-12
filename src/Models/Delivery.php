<?php
namespace App\Models;

use App\Core\Model;
use PDO;

class Delivery extends Model
{
    protected $table = 'deliveries';

    /**
     * Crear una nueva entrega
     */
    public function createDelivery(array $data)
    {
        return $this->create($data);
    }

    /**
     * Listar todas las entregas con datos relacionados
     */
    public function findAllWithRelations()
    {
        $sql = "
            SELECT d.*, 
                   i.name AS institution_name,
                   r.date AS reception_date,
                   a.full_name AS receiver_name
            FROM deliveries d
            INNER JOIN institutions i ON d.institution_id = i.id
            LEFT JOIN receptions r ON d.reception_id = r.id
            INNER JOIN actors a ON d.receiver_id = a.id
            ORDER BY d.date DESC
        ";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Buscar entrega por ID con relaciones
     */
    public function findByIdWithRelations(int $id)
    {
        $sql = "
            SELECT d.*, 
                   i.name AS institution_name,
                   r.date AS reception_date,
                   a.full_name AS receiver_name
            FROM deliveries d
            INNER JOIN institutions i ON d.institution_id = i.id
            LEFT JOIN receptions r ON d.reception_id = r.id
            INNER JOIN actors a ON d.receiver_id = a.id
            WHERE d.id = ?
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }
}
