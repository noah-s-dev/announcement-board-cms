<?php
/**
 * Database Connection and Management Class
 * Handles all database operations for the Announcement Board CMS
 */

class Database {
    private $host;
    private $db_name;
    private $username;
    private $password;
    private $charset;
    private $pdo;
    
    public function __construct() {
        $this->host = DB_HOST;
        $this->db_name = DB_NAME;
        $this->username = DB_USER;
        $this->password = DB_PASS;
        $this->charset = DB_CHARSET;
    }
    
    /**
     * Create database connection
     * @return PDO|null
     */
    public function connect() {
        $this->pdo = null;
        
        try {
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=" . $this->charset;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            $this->pdo = new PDO($dsn, $this->username, $this->password, $options);
        } catch (PDOException $e) {
            echo "Connection Error: " . $e->getMessage();
            return null;
        }
        
        return $this->pdo;
    }
    
    /**
     * Get the PDO connection
     * @return PDO|null
     */
    public function getConnection() {
        if ($this->pdo === null) {
            return $this->connect();
        }
        return $this->pdo;
    }
    
    /**
     * Execute a prepared statement
     * @param string $query
     * @param array $params
     * @return PDOStatement|false
     */
    public function query($query, $params = []) {
        try {
            $pdo = $this->getConnection();
            if ($pdo === null) {
                return false;
            }
            
            $stmt = $pdo->prepare($query);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            echo "Query Error: " . $e->getMessage();
            return false;
        }
    }
    
    /**
     * Get single record
     * @param string $query
     * @param array $params
     * @return array|false
     */
    public function fetch($query, $params = []) {
        $stmt = $this->query($query, $params);
        if ($stmt === false) {
            return false;
        }
        return $stmt->fetch();
    }
    
    /**
     * Get multiple records
     * @param string $query
     * @param array $params
     * @return array|false
     */
    public function fetchAll($query, $params = []) {
        $stmt = $this->query($query, $params);
        if ($stmt === false) {
            return false;
        }
        return $stmt->fetchAll();
    }
    
    /**
     * Get last inserted ID
     * @return string|false
     */
    public function lastInsertId() {
        $pdo = $this->getConnection();
        if ($pdo === null) {
            return false;
        }
        return $pdo->lastInsertId();
    }
    
    /**
     * Get row count from last statement
     * @param PDOStatement $stmt
     * @return int
     */
    public function rowCount($stmt) {
        return $stmt->rowCount();
    }
    
    /**
     * Begin transaction
     * @return bool
     */
    public function beginTransaction() {
        $pdo = $this->getConnection();
        if ($pdo === null) {
            return false;
        }
        return $pdo->beginTransaction();
    }
    
    /**
     * Commit transaction
     * @return bool
     */
    public function commit() {
        $pdo = $this->getConnection();
        if ($pdo === null) {
            return false;
        }
        return $pdo->commit();
    }
    
    /**
     * Rollback transaction
     * @return bool
     */
    public function rollback() {
        $pdo = $this->getConnection();
        if ($pdo === null) {
            return false;
        }
        return $pdo->rollback();
    }
}
?>

