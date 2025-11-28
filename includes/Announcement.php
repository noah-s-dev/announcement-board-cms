<?php
/**
 * Announcement Model Class
 * Handles announcement operations for the Announcement Board CMS
 */

require_once 'Database.php';

class Announcement {
    private $db;
    private $table = 'announcements';
    
    public $id;
    public $title;
    public $content;
    public $admin_id;
    public $is_published;
    public $created_at;
    public $updated_at;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    /**
     * Get all published announcements for public view
     * @param int $limit
     * @param int $offset
     * @return array|false
     */
    public function getPublished($limit = null, $offset = 0) {
        $query = "SELECT a.*, ad.username as admin_username 
                  FROM " . $this->table . " a 
                  LEFT JOIN admins ad ON a.admin_id = ad.id 
                  WHERE a.is_published = 1 
                  ORDER BY a.created_at DESC";
        
        if ($limit !== null) {
            $query .= " LIMIT " . intval($limit) . " OFFSET " . intval($offset);
        }
        
        return $this->db->fetchAll($query);
    }
    
    /**
     * Get all announcements for admin view
     * @param int $limit
     * @param int $offset
     * @return array|false
     */
    public function getAll($limit = null, $offset = 0) {
        $query = "SELECT a.*, ad.username as admin_username 
                  FROM " . $this->table . " a 
                  LEFT JOIN admins ad ON a.admin_id = ad.id 
                  ORDER BY a.created_at DESC";
        
        if ($limit !== null) {
            $query .= " LIMIT " . intval($limit) . " OFFSET " . intval($offset);
        }
        
        return $this->db->fetchAll($query);
    }
    
    /**
     * Get announcement by ID
     * @param int $id
     * @return array|false
     */
    public function getById($id) {
        $query = "SELECT a.*, ad.username as admin_username 
                  FROM " . $this->table . " a 
                  LEFT JOIN admins ad ON a.admin_id = ad.id 
                  WHERE a.id = ? LIMIT 1";
        return $this->db->fetch($query, [$id]);
    }
    
    /**
     * Get published announcement by ID
     * @param int $id
     * @return array|false
     */
    public function getPublishedById($id) {
        $query = "SELECT a.*, ad.username as admin_username 
                  FROM " . $this->table . " a 
                  LEFT JOIN admins ad ON a.admin_id = ad.id 
                  WHERE a.id = ? AND a.is_published = 1 LIMIT 1";
        return $this->db->fetch($query, [$id]);
    }
    
    /**
     * Create new announcement
     * @param string $title
     * @param string $content
     * @param int $admin_id
     * @param bool $is_published
     * @return int|false
     */
    public function create($title, $content, $admin_id, $is_published = true) {
        $query = "INSERT INTO " . $this->table . " (title, content, admin_id, is_published) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->query($query, [$title, $content, $admin_id, $is_published ? 1 : 0]);
        
        if ($stmt !== false) {
            return $this->db->lastInsertId();
        }
        
        return false;
    }
    
    /**
     * Update announcement
     * @param int $id
     * @param string $title
     * @param string $content
     * @param bool $is_published
     * @return bool
     */
    public function update($id, $title, $content, $is_published = true) {
        $query = "UPDATE " . $this->table . " SET title = ?, content = ?, is_published = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
        $stmt = $this->db->query($query, [$title, $content, $is_published ? 1 : 0, $id]);
        
        return $stmt !== false && $this->db->rowCount($stmt) > 0;
    }
    
    /**
     * Delete announcement
     * @param int $id
     * @return bool
     */
    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->db->query($query, [$id]);
        
        return $stmt !== false && $this->db->rowCount($stmt) > 0;
    }
    
    /**
     * Toggle announcement published status
     * @param int $id
     * @return bool
     */
    public function togglePublished($id) {
        $query = "UPDATE " . $this->table . " SET is_published = NOT is_published, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
        $stmt = $this->db->query($query, [$id]);
        
        return $stmt !== false && $this->db->rowCount($stmt) > 0;
    }
    
    /**
     * Get total count of published announcements
     * @return int
     */
    public function getPublishedCount() {
        $query = "SELECT COUNT(*) as count FROM " . $this->table . " WHERE is_published = 1";
        $result = $this->db->fetch($query);
        return $result ? intval($result['count']) : 0;
    }
    
    /**
     * Get total count of all announcements
     * @return int
     */
    public function getTotalCount() {
        $query = "SELECT COUNT(*) as count FROM " . $this->table;
        $result = $this->db->fetch($query);
        return $result ? intval($result['count']) : 0;
    }
    
    /**
     * Search announcements
     * @param string $search_term
     * @param bool $published_only
     * @return array|false
     */
    public function search($search_term, $published_only = true) {
        $where_clause = $published_only ? "WHERE a.is_published = 1 AND" : "WHERE";
        
        $query = "SELECT a.*, ad.username as admin_username 
                  FROM " . $this->table . " a 
                  LEFT JOIN admins ad ON a.admin_id = ad.id 
                  " . $where_clause . " (a.title LIKE ? OR a.content LIKE ?) 
                  ORDER BY a.created_at DESC";
        
        $search_param = '%' . $search_term . '%';
        return $this->db->fetchAll($query, [$search_param, $search_param]);
    }
}
?>

