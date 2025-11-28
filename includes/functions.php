<?php
/**
 * Utility Functions for Announcement Board CMS
 * Contains helper functions used throughout the application
 */

/**
 * Sanitize input data
 * @param string $data
 * @return string
 */
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Validate email address
 * @param string $email
 * @return bool
 */
function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Generate CSRF token
 * @return string
 */
function generate_csrf_token() {
    if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
    }
    return $_SESSION[CSRF_TOKEN_NAME];
}

/**
 * Verify CSRF token
 * @param string $token
 * @return bool
 */
function verify_csrf_token($token) {
    return isset($_SESSION[CSRF_TOKEN_NAME]) && hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
}

/**
 * Check if user is logged in as admin
 * @return bool
 */
function is_admin_logged_in() {
    return isset($_SESSION['admin_id']) && isset($_SESSION['admin_username']);
}

/**
 * Get current admin ID
 * @return int|null
 */
function get_current_admin_id() {
    return isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : null;
}

/**
 * Get current admin username
 * @return string|null
 */
function get_current_admin_username() {
    return isset($_SESSION['admin_username']) ? $_SESSION['admin_username'] : null;
}

/**
 * Redirect to a specific page
 * Handles both relative and absolute URLs, ensuring proper subdirectory support
 * @param string $url
 */
function redirect($url) {
    // If URL is already absolute (starts with http:// or https://), use it as-is
    if (preg_match('/^https?:\/\//', $url)) {
        header("Location: " . $url);
        exit();
    }
    
    // If URL starts with /, it's an absolute path from domain root
    // We need to prepend BASE_URL to handle subdirectories
    if (strpos($url, '/') === 0) {
        $base_url = defined('BASE_URL') ? BASE_URL : '';
        header("Location: " . $base_url . $url);
        exit();
    }
    
    // For relative URLs (like 'dashboard.php', 'login.php'), use them as-is
    // The browser will resolve them relative to the current directory
    header("Location: " . $url);
    exit();
}

/**
 * Format date for display
 * @param string $date
 * @param string $format
 * @return string
 */
function format_date($date, $format = 'F j, Y g:i A') {
    return date($format, strtotime($date));
}

/**
 * Truncate text to specified length
 * @param string $text
 * @param int $length
 * @param string $suffix
 * @return string
 */
function truncate_text($text, $length = 150, $suffix = '...') {
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . $suffix;
}

/**
 * Escape HTML output
 * @param string $text
 * @return string
 */
function escape_html($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

/**
 * Convert line breaks to HTML breaks
 * @param string $text
 * @return string
 */
function nl2br_safe($text) {
    return nl2br(escape_html($text));
}

/**
 * Generate pagination links
 * @param int $current_page
 * @param int $total_pages
 * @param string $base_url
 * @return string
 */
function generate_pagination($current_page, $total_pages, $base_url) {
    if ($total_pages <= 1) {
        return '';
    }
    
    $html = '<nav aria-label="Page navigation"><ul class="pagination justify-content-center">';
    
    // Previous button
    if ($current_page > 1) {
        $prev_page = $current_page - 1;
        $html .= '<li class="page-item"><a class="page-link" href="' . $base_url . '?page=' . $prev_page . '">Previous</a></li>';
    } else {
        $html .= '<li class="page-item disabled"><span class="page-link">Previous</span></li>';
    }
    
    // Page numbers
    for ($i = 1; $i <= $total_pages; $i++) {
        if ($i == $current_page) {
            $html .= '<li class="page-item active"><span class="page-link">' . $i . '</span></li>';
        } else {
            $html .= '<li class="page-item"><a class="page-link" href="' . $base_url . '?page=' . $i . '">' . $i . '</a></li>';
        }
    }
    
    // Next button
    if ($current_page < $total_pages) {
        $next_page = $current_page + 1;
        $html .= '<li class="page-item"><a class="page-link" href="' . $base_url . '?page=' . $next_page . '">Next</a></li>';
    } else {
        $html .= '<li class="page-item disabled"><span class="page-link">Next</span></li>';
    }
    
    $html .= '</ul></nav>';
    
    return $html;
}

/**
 * Display flash messages
 * @param string $type
 * @return string
 */
function display_flash_message($type = 'all') {
    $html = '';
    
    if ($type === 'all' || $type === 'success') {
        if (isset($_SESSION['success_message'])) {
            $html .= '<div class="alert alert-success alert-dismissible fade show" role="alert">';
            $html .= escape_html($_SESSION['success_message']);
            $html .= '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
            $html .= '</div>';
            unset($_SESSION['success_message']);
        }
    }
    
    if ($type === 'all' || $type === 'error') {
        if (isset($_SESSION['error_message'])) {
            $html .= '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
            $html .= escape_html($_SESSION['error_message']);
            $html .= '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
            $html .= '</div>';
            unset($_SESSION['error_message']);
        }
    }
    
    if ($type === 'all' || $type === 'info') {
        if (isset($_SESSION['info_message'])) {
            $html .= '<div class="alert alert-info alert-dismissible fade show" role="alert">';
            $html .= escape_html($_SESSION['info_message']);
            $html .= '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
            $html .= '</div>';
            unset($_SESSION['info_message']);
        }
    }
    
    return $html;
}

/**
 * Set flash message
 * @param string $message
 * @param string $type
 */
function set_flash_message($message, $type = 'success') {
    $_SESSION[$type . '_message'] = $message;
}
?>

