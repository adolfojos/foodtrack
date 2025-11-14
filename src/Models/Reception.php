<?php
namespace App\Models;

use App\Core\Model;
use PDO;

class Reception extends Model
{
    protected $table = 'receptions';

    /**
     * Crear una nueva recepción
     * NOTA: Se implementa la lógica de inserción explícita para garantizar 
     * que se devuelve el ID de la última inserción (lastInsertId).
     */
    public function createReception(array $data)
    {
        // Campos que esperamos y queremos insertar
        $fields = [
            'date', 
            'inspector_id', 
            'vocero_parroquial_id', 
            'reception_type', 
            'summary_quantity', 
            'notes'
        ];
        
        // Filtramos y preparamos los datos
        $filteredData = array_intersect_key($data, array_flip($fields));
        $cols = implode(', ', array_keys($filteredData));
        $placeholders = implode(', ', array_fill(0, count($filteredData), '?'));

        $sql = "INSERT INTO {$this->table} ({$cols}) VALUES ({$placeholders})";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute(array_values($filteredData));

            if ($result) {
                // Devolver el ID de la ÚLTIMA INSERCIÓN (clave para la relación padre-hijo)
                return (int) $this->pdo->lastInsertId();
            }
            return false; // Error en la ejecución de la consulta
        } catch (\PDOException $e) {
            // Registra el error en el log de PHP para depuración
            error_log("Error al crear la recepción (Cabecera): " . $e->getMessage());
            // Si hay un error, devolvemos false para que el controlador lo maneje
            return false;
        }
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

    // =======================================================
    // FUNCIONALIDAD: Obtener y Guardar los ítems de una recepción
    // =======================================================

    /**
     * Obtener los ítems (productos) asociados a una recepción por su ID.
     */
    public function getItemsByReceptionId(int $receptionId)
    {
        $sql = "
            SELECT *
            FROM reception_items
            WHERE reception_id = ?
            ORDER BY product_name ASC
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$receptionId]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Guardar un item de recepción
     * @param int $receptionId El ID de la recepción.
     * @param array $itemData Array con product_name, quantity, unit.
     */
    public function createReceptionItem(int $receptionId, array $itemData)
    {
        $sql = "
            INSERT INTO reception_items (reception_id, product_name, quantity, unit)
            VALUES (?, ?, ?, ?)
        ";
        $stmt = $this->pdo->prepare($sql);
        // **Línea 98 del archivo original: Ahora la falla debería ser imposible 
        // si receptionId > 0 y es un ID válido en la tabla receptions.**
        return $stmt->execute([
            $receptionId, 
            $itemData['product_name'], 
            $itemData['quantity'], 
            $itemData['unit']
        ]);
    }
}