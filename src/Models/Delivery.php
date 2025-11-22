<?php
namespace App\Models;

use App\Core\Model;
use PDO;

class Delivery extends Model
{
    protected $table = 'deliveries';

    /**
     * Crear cabecera de entrega
     */
    public function createDelivery(array $data)
    {
        $fields = ['date', 'institution_id', 'reception_id', 'receiver_id', 'qty_groceries', 'qty_proteins', 'qty_fruits', 'qty_vegetables', 'receiver_signature'];
        
        $filteredData = array_intersect_key($data, array_flip($fields));
        $cols = implode(', ', array_keys($filteredData));
        $placeholders = implode(', ', array_fill(0, count($filteredData), '?'));
        
        $sql = "INSERT INTO {$this->table} ({$cols}) VALUES ({$placeholders})";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(array_values($filteredData));
            return (int) $this->pdo->lastInsertId();
        } catch (\PDOException $e) {
            error_log("Error createDelivery: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Guardar ítem de entrega (Descontar inventario detallado)
     */
    public function createDeliveryItem(int $deliveryId, int $productId, float $quantity, string $unit)
    {
        $sql = "INSERT INTO delivery_items (delivery_id, product_id, quantity, unit) 
                VALUES (?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE quantity = VALUES(quantity)";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$deliveryId, $productId, $quantity, $unit]);
    }

    /**
     * Listar todas las entregas con nombres de instituciones y actores
     */
    public function findAllWithDetails()
    {
        $sql = "SELECT d.*, 
                       i.name as institution_name,
                       r.reception_type,
                       act.full_name as receiver_name
                FROM deliveries d
                INNER JOIN institutions i ON d.institution_id = i.id
                LEFT JOIN receptions r ON d.reception_id = r.id
                INNER JOIN actors act ON d.receiver_id = act.id
                ORDER BY d.date DESC";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Obtener una entrega específica por ID
     */
    public function findByIdWithDetails(int $id)
    {
        $sql = "SELECT d.*, 
                       i.name as institution_name,
                       i.parish,
                       r.reception_type,
                       r.summary_quantity as source_bags_total,
                       act.full_name as receiver_name
                FROM deliveries d
                INNER JOIN institutions i ON d.institution_id = i.id
                LEFT JOIN receptions r ON d.reception_id = r.id
                INNER JOIN actors act ON d.receiver_id = act.id
                WHERE d.id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    /**
     * Obtener los items de una entrega
     */
    public function getItemsByDeliveryId(int $deliveryId)
    {
        $sql = "SELECT di.*, p.name as product_name
                FROM delivery_items di
                INNER JOIN products p ON di.product_id = p.id
                WHERE di.delivery_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$deliveryId]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * CLAVE: Obtener datos de la Recepción Origen para calcular el contenido de la bolsa.
     */
    public function getSourceReceptionData(int $receptionId)
    {
        // 1. Cabecera (necesitamos summary_quantity para saber cuántas bolsas llegaron)
        $stmt = $this->pdo->prepare("SELECT * FROM receptions WHERE id = ?");
        $stmt->execute([$receptionId]);
        $reception = $stmt->fetch(PDO::FETCH_OBJ);

        if (!$reception) return null;

        // 2. Items (el contenido total recibido)
        // Si llegaron 386 bolsas y 1158 Arroz, aquí obtenemos esos 1158.
        $stmtItems = $this->pdo->prepare("
            SELECT ri.product_id, ri.quantity as total_qty, ri.unit, p.name 
            FROM reception_items ri
            JOIN products p ON ri.product_id = p.id
            WHERE ri.reception_id = ?
        ");
        $stmtItems->execute([$receptionId]);
        $items = $stmtItems->fetchAll(PDO::FETCH_OBJ);

        return [
            'header' => $reception,
            'items' => $items
        ];
    }

    /**
     * Obtener matrícula total activa (Universo) para el cálculo
     * Retorna la suma de 'total_enrollment' de todas las escuelas activas.
     */
    public function getTotalActiveEnrollment()
    {
        $stmt = $this->pdo->query("SELECT SUM(total_enrollment) FROM institutions WHERE active = 1");
        return (int) $stmt->fetchColumn();
    }
    
    public function deleteDelivery(int $id) {
         // Items se borran por CASCADE si la FK está configurada, pero aseguramos:
         $this->pdo->prepare("DELETE FROM delivery_items WHERE delivery_id = ?")->execute([$id]);
         $stmt = $this->pdo->prepare("DELETE FROM deliveries WHERE id = ?");
         return $stmt->execute([$id]);
    }
    public function getActiveInstitutions()
    {
        $sql = "SELECT id, name, total_enrollment FROM institutions WHERE active = 1 ORDER BY name ASC";
        // Aquí sí podemos usar $this->pdo porque estamos DENTRO del modelo
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_OBJ);
    }

/**
 * Eliminar solo los ítems de una entrega (útil para actualizaciones)
 */
    public function clearItems(int $deliveryId)
    {
        $sql = "DELETE FROM delivery_items WHERE delivery_id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$deliveryId]);
    }
}