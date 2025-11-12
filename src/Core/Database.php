<?php
namespace App\Core;

use PDO;
use PDOException;

class Database {
    private static $instance = null;
    private $pdo;
    private $dsn;

    private function __construct() {
        // Incluir las constantes de configuración
        require_once __DIR__ . '/../../config/database.php';
        
        $this->dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->pdo = new PDO($this->dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // En un sistema real, esto debería loguearse, no mostrarse.
            throw new PDOException("Connection error: " . $e->getMessage());
        }
    }

    // El método Singleton para obtener la instancia
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // Método para obtener la conexión PDO
    public function getConnection() {
        return $this->pdo;
    }
}