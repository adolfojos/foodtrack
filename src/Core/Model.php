<?php
namespace App\Core;
use PDO;

abstract class Model {
    protected $pdo;
    protected $table; // Cada modelo hijo definirá su tabla

    public function __construct() {
        // Obtenemos la conexión PDO del Singleton
        $this->pdo = Database::getInstance()->getConnection();
    }

    // Ejemplo de método común: buscar todo
   // public function findAll() {
      //  $stmt = $this->pdo->query("SELECT * FROM {$this->table}");
        //return $stmt->fetchAll(PDO::FETCH_OBJ); // Devolver como objetos
    //}

    // Ejemplo de método común: buscar por ID
    //public function findById($id) {
      //  $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE id = ?");
       // $stmt->execute([$id]);
       // return $stmt->fetch(PDO::FETCH_OBJ);
    //}
    
    // ------------------------------------------------------------------
    // Métodos CRUD Generales
    // ------------------------------------------------------------------

    /**
     * Inserta un nuevo registro en la tabla.
     * @param array $data ['columna' => 'valor', ...]
     * @return bool
     */
    public function create(array $data) {
        // Genera los placeholders (?, ?, ...) y los nombres de columnas (col1, col2)
        $fields = array_keys($data);
        $placeholders = str_repeat('?,', count($fields) - 1) . '?';
        $columns = implode(', ', $fields);

        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        
        $stmt = $this->pdo->prepare($sql);
        // La función execute espera los valores del array $data
        return $stmt->execute(array_values($data));
    }
    
    /**
     * Busca todos los registros.
     * @return array
     */
    public function findAll() {
        $sql = "SELECT * FROM {$this->table} ORDER BY id DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
    
    /**
     * Busca un registro por su ID.
     * @param int $id
     * @return object|null
     */
    public function findById(int $id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    /**
     * Actualiza un registro existente.
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data) {
        // Genera la cadena de 'columna = ?'
        $setClauses = [];
        foreach (array_keys($data) as $field) {
            $setClauses[] = "{$field} = ?";
        }
        $set = implode(', ', $setClauses);

        $sql = "UPDATE {$this->table} SET {$set} WHERE id = ?";
        
        // Los valores son los datos más el ID
        $values = array_values($data);
        $values[] = $id;

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($values);
    }

    /**
     * Elimina un registro por ID.
     * @param int $id
     * @return bool
     */
    public function delete(int $id) {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }
}