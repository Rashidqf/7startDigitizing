<?php
/**
 * Secure Database Connection Class
 * Uses PDO with prepared statements and connection pooling
 */

class Database {
    private static $instance = null;
    private $connection;
    private $config;
    
    private function __construct() {
        $this->config = Config::get('database');
        $this->connect();
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function connect() {
        try {
            $dsn = "mysql:host={$this->config['host']};dbname={$this->config['name']};charset={$this->config['charset']}";
            
            $this->connection = new PDO(
                $dsn,
                $this->config['username'],
                $this->config['password'],
                $this->config['options']
            );
            
            // Set additional security options
            $this->connection->exec("SET SESSION sql_mode = 'STRICT_TRANS_TABLES,NO_ZERO_DATE,NO_ZERO_IN_DATE,ERROR_FOR_DIVISION_BY_ZERO'");
            
        } catch (PDOException $e) {
            $this->logError('Database connection failed: ' . $e->getMessage());
            throw new Exception('Database connection failed. Please try again later.');
        }
    }
    
    public function getConnection() {
        if (!$this->connection || $this->connection->errorInfo()[0] !== '00000') {
            $this->connect();
        }
        return $this->connection;
    }
    
    public function prepare($sql) {
        try {
            $stmt = $this->getConnection()->prepare($sql);
            if (!$stmt) {
                throw new Exception('Failed to prepare statement');
            }
            return $stmt;
        } catch (PDOException $e) {
            $this->logError('Statement preparation failed: ' . $e->getMessage());
            throw new Exception('Database operation failed. Please try again later.');
        }
    }
    
    public function execute($sql, $params = []) {
        try {
            $stmt = $this->prepare($sql);
            $result = $stmt->execute($params);
            
            if (!$result) {
                $error = $stmt->errorInfo();
                $this->logError('Statement execution failed: ' . $error[2]);
                throw new Exception('Database operation failed. Please try again later.');
            }
            
            return $stmt;
        } catch (PDOException $e) {
            $this->logError('Statement execution failed: ' . $e->getMessage());
            throw new Exception('Database operation failed. Please try again later.');
        }
    }
    
    public function query($sql, $params = []) {
        $stmt = $this->execute($sql, $params);
        return $stmt->fetchAll();
    }
    
    public function queryOne($sql, $params = []) {
        $stmt = $this->execute($sql, $params);
        return $stmt->fetch();
    }
    
    public function insert($sql, $params = []) {
        $stmt = $this->execute($sql, $params);
        return $this->getConnection()->lastInsertId();
    }
    
    public function update($sql, $params = []) {
        $stmt = $this->execute($sql, $params);
        return $stmt->rowCount();
    }
    
    public function delete($sql, $params = []) {
        $stmt = $this->execute($sql, $params);
        return $stmt->rowCount();
    }
    
    public function beginTransaction() {
        return $this->getConnection()->beginTransaction();
    }
    
    public function commit() {
        return $this->getConnection()->commit();
    }
    
    public function rollback() {
        return $this->getConnection()->rollback();
    }
    
    public function inTransaction() {
        return $this->getConnection()->inTransaction();
    }
    
    private function logError($message) {
        if (Config::get('app.debug')) {
            error_log($message);
        }
        // In production, you might want to send this to a logging service
    }
    
    public function __destruct() {
        $this->connection = null;
    }
    
    // Prevent cloning
    private function __clone() {}
    
    // Prevent unserialization
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}
