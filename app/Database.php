<?php
namespace App;

use PDO;
use Exception;

class Database {
    private static $instance = null;
    private $pdo;
    
    private function __construct() {
        try {
            $config = require __DIR__ . '/../config/config.php';
            
            $dsn = sprintf(
                "mysql:host=%s;dbname=%s;charset=%s",
                $config['db']['host'],
                $config['db']['name'],
                $config['db']['charset']
            );
            
            $this->pdo = new PDO($dsn, $config['db']['user'], $config['db']['pass'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]);
        } catch (Exception $e) {
            error_log("Database connection failed: " . $e->getMessage());
            throw new Exception("Database connection failed");
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->pdo;
    }
    
    public function query($sql, $params = []) {
        try {
            // Validation : params doit être un tableau
            if (!is_array($params)) {
                error_log("Database::query - params is not an array, converting to array");
                $params = [$params];
            }
            
            // Validation : aucune valeur ne peut être un tableau
            foreach ($params as $key => $value) {
                if (is_array($value)) {
                    error_log("Database::query - Array detected at index $key: " . print_r($value, true));
                    error_log("SQL: " . $sql);
                    throw new Exception("Query parameter at index $key cannot be an array. Use scalar values only.");
                }
            }
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt;
            
        } catch (Exception $e) {
            error_log("Database query error: " . $e->getMessage());
            error_log("SQL: " . $sql);
            error_log("Params: " . print_r($params, true));
            throw $e;
        }
    }
    
    public function fetchAll($sql, $params = []) {
        try {
            return $this->query($sql, $params)->fetchAll();
        } catch (Exception $e) {
            error_log("Database fetchAll error: " . $e->getMessage());
            return [];
        }
    }
    
    public function fetchOne($sql, $params = []) {
        try {
            $result = $this->query($sql, $params)->fetch();
            return $result ?: null;
        } catch (Exception $e) {
            error_log("Database fetchOne error: " . $e->getMessage());
            return null;
        }
    }
    
    public function insert($sql, $params = []) {
        try {
            $this->query($sql, $params);
            return $this->pdo->lastInsertId();
        } catch (Exception $e) {
            error_log("Database insert error: " . $e->getMessage());
            throw $e;
        }
    }
    
    public function beginTransaction() {
        return $this->pdo->beginTransaction();
    }
    
    public function commit() {
        return $this->pdo->commit();
    }
    
    public function rollback() {
        return $this->pdo->rollBack();
    }
}