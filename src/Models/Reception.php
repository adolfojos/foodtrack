<?php
namespace App\Models;

use App\Core\Model;
use PDO;

class Reception extends Model
{
    protected $table = 'receptions';

    /**
     * Crear cabecera de recepción
     */
    public function createReception(array $data)
    {
        $fields = ['date', 'inspector_id', 'vocero_parroquial_id', 'reception_type', 'summary_quantity', 'notes'];
        
        $filteredData = array_intersect_key($data, array_flip($fields));
        $cols = implode(', ', array_keys($filteredData));
        $placeholders = implode(', ', array_fill(0, count($filteredData), '?'));

        $sql = "INSERT INTO {$this->table} ({$cols}) VALUES ({$placeholders})";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(array_values($filteredData));
            return (int) $this->pdo->lastInsertId();
        } catch (\PDOException $e) {
            error_log("Error createReception: " . $e->getMessage());
            return false;
        }
    }

    /**
     * LÓGICA CLAVE: Busca un producto por nombre.
     * Si existe, devuelve su ID. Si no, lo crea y devuelve el nuevo ID.
     */
    public function getOrCreateProduct(string $name, string $unit)
    {
        $name = trim($name);
        
        // 1. Buscar
        $sql = "SELECT id FROM products WHERE name = ? LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$name]);
        $product = $stmt->fetch(PDO::FETCH_OBJ);

        if ($product) {
            return (int) $product->id;
        }

        // 2. Crear si no existe
        $sqlInsert = "INSERT INTO products (name, default_unit) VALUES (?, ?)";
        $stmtInsert = $this->pdo->prepare($sqlInsert);
        
        try {
            $stmtInsert->execute([$name, $unit]);
            return (int) $this->pdo->lastInsertId();
        } catch (\PDOException $e) {
            return false; 
        }
    }

    /**
     * Guardar item (usando ID de producto y cantidad total calculada)
     */
    public function createReceptionItem(int $receptionId, int $productId, float $quantity, string $unit)
    {
        // Se usa IGNORE o ON DUPLICATE UPDATE para evitar errores si intentan meter el mismo producto 2 veces
        $sql = "INSERT INTO reception_items (reception_id, product_id, quantity, unit) 
                VALUES (?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity)";
                
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$receptionId, $productId, $quantity, $unit]);
    }

    /**
     * Obtener datos completos para ver detalles
     */
    public function findByIdWithActors(int $id)
    {
        $sql = "SELECT r.*, 
                       i.full_name AS inspector_name, 
                       p.full_name AS vocero_parroquial_name
                FROM receptions r
                INNER JOIN actors i ON r.inspector_id = i.id
                INNER JOIN actors p ON r.vocero_parroquial_id = p.id
                WHERE r.id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function findAllWithActors()
    {
        $sql = "SELECT r.*, 
                       i.full_name AS inspector_name, 
                       p.full_name AS vocero_parroquial_name
                FROM receptions r
                INNER JOIN actors i ON r.inspector_id = i.id
                INNER JOIN actors p ON r.vocero_parroquial_id = p.id
                ORDER BY r.date DESC";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_OBJ);
    }

    public function getItemsByReceptionId(int $receptionId)
    {
        // JOIN con products para mostrar el nombre real
        $sql = "SELECT ri.*, p.name as product_name 
                FROM reception_items ri
                INNER JOIN products p ON ri.product_id = p.id
                WHERE ri.reception_id = ?
                ORDER BY p.name ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$receptionId]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
    /**
     * Obtener lista simple de productos para el autocompletado
     */
    public function getAllProductNames()
    {
        $sql = "SELECT name FROM products ORDER BY name ASC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_COLUMN); // Devuelve un array simple ['Arroz', 'Azucar'...]
    }
    /**
     * Actualizar la cabecera de la recepción
     */
    public function updateReception(int $id, array $data)
    {
        $fields = ['date', 'inspector_id', 'vocero_parroquial_id', 'reception_type', 'summary_quantity', 'notes'];
        $filteredData = array_intersect_key($data, array_flip($fields));
        
        // Añadimos el ID al final para el WHERE
        $values = array_values($filteredData);
        $values[] = $id;

        $cols = implode(' = ?, ', array_keys($filteredData)) . ' = ?';

        $sql = "UPDATE {$this->table} SET {$cols} WHERE id = ?";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($values);
    }

    /**
     * Eliminar TODOS los items de una recepción (para re-insertarlos actualizados)
     */
    public function deleteAllItemsByReceptionId(int $receptionId)
    {
        $sql = "DELETE FROM reception_items WHERE reception_id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$receptionId]);
    }
    /**
     * Elimina una recepción y todos sus ítems asociados.
     * @param int $id El ID de la recepción a eliminar.
     * @return bool True si la eliminación fue exitosa.
     */
    public function deleteReception(int $id): bool {
        try {
            // 1. Iniciar transacción (para asegurar que si algo falla, no se borre nada)
            $this->pdo->beginTransaction();

            // 2. Eliminar todos los ítems de la recepción
            $stmtItems = $this->pdo->prepare("DELETE FROM reception_items WHERE reception_id = :id");
            $stmtItems->execute([':id' => $id]);

            // 3. Eliminar la recepción principal
            $stmtReception = $this->pdo->prepare("DELETE FROM {$this->table} WHERE id = :id");
            $result = $stmtReception->execute([':id' => $id]);

            // 4. Si todo salió bien, confirmar
            $this->pdo->commit();
            return $result;

        } catch (\PDOException $e) {
            // 5. Si algo falla, revertir los cambios
            $this->pdo->rollBack();
            // Opcional: Loguear o manejar el error
            // throw $e; 
            return false;
        }
    }

}