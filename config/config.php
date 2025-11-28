<?php
/**
 * Announcement Board CMS Configuration File
 * Contains database connection settings and other configuration options
 */

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'announcement_board');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Application Configuration
define('APP_NAME', 'Announcement Board CMS');
define('APP_VERSION', '1.0.0');
define('TIMEZONE', 'UTC');

// Base URL Configuration - Auto-detect project path
// This ensures redirects work correctly whether the project is in root or subdirectory
if (!defined('BASE_URL')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    
    // Remove port from host if present (to avoid port 8080 issues)
    $host = preg_replace('/:\d+$/', '', $host);
    
    // Get the document root and project root
    $document_root = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT'] ?? '');
    $project_root = str_replace('\\', '/', dirname(__DIR__));
    
    // Calculate the base path (project directory relative to document root)
    $base_path = str_replace($document_root, '', $project_root);
    $base_path = str_replace('\\', '/', $base_path);
    $base_path = rtrim($base_path, '/');
    
    // If base_path is empty, we're in the document root
    if (empty($base_path)) {
        $base_path = '';
    }
    
    // Construct BASE_URL without port
    $base_url = $protocol . $host . $base_path;
    define('BASE_URL', rtrim($base_url, '/'));
}

// Security Configuration
define('SESSION_NAME', 'announcement_board_session');
define('CSRF_TOKEN_NAME', 'csrf_token');
define('PASSWORD_MIN_LENGTH', 6);

// Pagination Configuration
define('ANNOUNCEMENTS_PER_PAGE', 10);

// File Upload Configuration (for future use)
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('UPLOAD_PATH', '../assets/uploads/');

// Set timezone
date_default_timezone_set(TIMEZONE);

// Include security class
require_once __DIR__ . '/../includes/Security.php';

// Set security headers
Security::setSecurityHeaders();

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_name(SESSION_NAME);
    session_start();
    
    // Clean old session data
    Security::cleanOldSessions();
}

// Error reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

