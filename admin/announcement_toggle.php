<?php
/**
 * Toggle Announcement Status
 * Toggles announcement between published and draft
 */

require_once '../config/config.php';
require_once '../includes/functions.php';
require_once '../includes/Announcement.php';

// Check if admin is logged in
if (!is_admin_logged_in()) {
    redirect('login.php');
}

// Get announcement ID
$announcement_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($announcement_id <= 0) {
    set_flash_message('Invalid announcement ID.', 'error');
    redirect('announcements.php');
}

$announcement = new Announcement();

// Check if announcement exists
$announcement_data = $announcement->getById($announcement_id);
if (!$announcement_data) {
    set_flash_message('Announcement not found.', 'error');
    redirect('announcements.php');
}

// Toggle the published status
$success = $announcement->togglePublished($announcement_id);

if ($success) {
    $new_status = $announcement_data['is_published'] ? 'unpublished' : 'published';
    set_flash_message("Announcement {$new_status} successfully.", 'success');
} else {
    set_flash_message('Failed to update announcement status. Please try again.', 'error');
}

// Redirect back to the referring page or announcements list
// Extract path from referer to avoid port/domain issues
if (isset($_SERVER['HTTP_REFERER'])) {
    $referer = $_SERVER['HTTP_REFERER'];
    // Extract path from full URL (remove protocol, domain, port)
    $parsed_url = parse_url($referer);
    $redirect_url = isset($parsed_url['path']) ? $parsed_url['path'] : 'announcements.php';
    
    // If the path is from a different domain or doesn't contain our admin path, use default
    if (strpos($redirect_url, '/admin/') === false && $redirect_url !== '/admin/announcements.php') {
        $redirect_url = 'announcements.php';
    } else {
        // Extract just the filename if it's in admin directory
        $redirect_url = basename($redirect_url);
    }
} else {
    $redirect_url = 'announcements.php';
}
redirect($redirect_url);
?>

