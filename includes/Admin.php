<?php
/**
 * Admin Model Class
 * Handles admin user operations for the Announcement Board CMS
 */

require_once 'Database.php';

class Admin {
    private $db;
    private $table = 'admins';
    
    public $id;
    public $username;
    public $email;
    public $password_hash;
    public $created_at;
    public $updated_at;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    /**
     * Authenticate admin user
     * @param string $username
     * @param string $password
     * @return array|false
     */
    public function authenticate($username, $password) {
        $query = "SELECT * FROM " . $this->table . " WHERE username = ? OR email = ? LIMIT 1";
        $admin = $this->db->fetch($query, [$username, $username]);
        
        if ($admin && password_verify($password, $admin['password_hash'])) {
            return $admin;
        }
        
        return false;
    }
    
    /**
     * Get admin by ID
     * @param int $id
     * @return array|false
     */
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = ? LIMIT 1";
        return $this->db->fetch($query, [$id]);
    }
    
    /**
     * Get admin by username
     * @param string $username
     * @return array|false
     */
    public function getByUsername($username) {
        $query = "SELECT * FROM " . $this->table . " WHERE username = ? LIMIT 1";
        return $this->db->fetch($query, [$username]);
    }
    
    /**
     * Get admin by email
     * @param string $email
     * @return array|false
     */
    public function getByEmail($email) {
        $query = "SELECT * FROM " . $this->table . " WHERE email = ? LIMIT 1";
        return $this->db->fetch($query, [$email]);
    }
    
    /**
     * Create new admin
     * @param string $username
     * @param string $email
     * @param string $password
     * @return bool
     */
    public function create($username, $email, $password) {
        // Check if username or email already exists
        if ($this->getByUsername($username) || $this->getByEmail($email)) {
            return false;
        }
        
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        
        $query = "INSERT INTO " . $this->table . " (username, email, password_hash) VALUES (?, ?, ?)";
        $stmt = $this->db->query($query, [$username, $email, $password_hash]);
        
        return $stmt !== false;
    }
    
    /**
     * Update admin password
     * @param int $id
     * @param string $new_password
     * @return bool
     */
    public function updatePassword($id, $new_password) {
        $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
        
        $query = "UPDATE " . $this->table . " SET password_hash = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
        $stmt = $this->db->query($query, [$password_hash, $id]);
        
        return $stmt !== false && $this->db->rowCount($stmt) > 0;
    }
    
    /**
     * Update admin profile
     * @param int $id
     * @param string $username
     * @param string $email
     * @return bool
     */
    public function updateProfile($id, $username, $email) {
        // Check if username or email already exists for other users
        $existing_username = $this->db->fetch("SELECT id FROM " . $this->table . " WHERE username = ? AND id != ?", [$username, $id]);
        $existing_email = $this->db->fetch("SELECT id FROM " . $this->table . " WHERE email = ? AND id != ?", [$email, $id]);
        
        if ($existing_username || $existing_email) {
            return false;
        }
        
        $query = "UPDATE " . $this->table . " SET username = ?, email = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
        $stmt = $this->db->query($query, [$username, $email, $id]);
        
        return $stmt !== false && $this->db->rowCount($stmt) > 0;
    }
    
    /**
     * Delete admin
     * @param int $id
     * @return bool
     */
    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->db->query($query, [$id]);
        
        return $stmt !== false && $this->db->rowCount($stmt) > 0;
    }
    
    /**
     * Get all admins
     * @return array|false
     */
    public function getAll() {
        $query = "SELECT id, username, email, created_at, updated_at FROM " . $this->table . " ORDER BY created_at DESC";
        return $this->db->fetchAll($query);
    }
    
    /**
     * Get admin count
     * @return int
     */
    public function getAdminCount() {
        $query = "SELECT COUNT(*) as count FROM " . $this->table;
        $result = $this->db->fetch($query);
        return $result ? (int)$result['count'] : 0;
    }
}
?>

