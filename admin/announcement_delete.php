<?php
/**
 * Delete Announcement
 * Handles announcement deletion
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

// Handle confirmation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete'])) {
    $success = $announcement->delete($announcement_id);
    
    if ($success) {
        set_flash_message('Announcement deleted successfully.', 'success');
    } else {
        set_flash_message('Failed to delete announcement. Please try again.', 'error');
    }
    
    redirect('announcements.php');
}

// If not POST request, redirect back (direct access not allowed)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('announcements.php');
}
?>

