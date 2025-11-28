<?php
/**
 * Admin Logout
 * Handles admin logout and session cleanup
 */

require_once '../config/config.php';
require_once '../includes/functions.php';

// Check if admin is logged in
if (!is_admin_logged_in()) {
    redirect('login.php');
}

// Clear all session variables
$_SESSION = array();

// Destroy the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Redirect to login page with success message
session_start();
set_flash_message('You have been successfully logged out.', 'info');
redirect('login.php');
?>

