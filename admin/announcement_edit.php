<?php
/**
 * Edit Announcement Page
 * Allows admins to edit existing announcements
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
$announcement_data = $announcement->getById($announcement_id);

if (!$announcement_data) {
    set_flash_message('Announcement not found.', 'error');
    redirect('announcements.php');
}

$error_message = '';
$title = $announcement_data['title'];
$content = $announcement_data['content'];
$is_published = $announcement_data['is_published'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitize_input($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $is_published = isset($_POST['is_published']) && $_POST['is_published'] === '1';
    
    // Validation
    if (empty($title)) {
        $error_message = 'Title is required.';
    } elseif (empty($content)) {
        $error_message = 'Content is required.';
    } elseif (strlen($title) > 255) {
        $error_message = 'Title must be less than 255 characters.';
    } else {
        $success = $announcement->update($announcement_id, $title, $content, $is_published);
        
        if ($success) {
            set_flash_message('Announcement updated successfully!', 'success');
            redirect('announcements.php');
        } else {
            $error_message = 'Failed to update announcement. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Announcement - <?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/admin.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="container-fluid">
        <div class="row">
            <?php include 'includes/sidebar.php'; ?>
            
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Edit Announcement</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <a href="../announcement.php?id=<?php echo $announcement_id; ?>" 
                               class="btn btn-outline-info" target="_blank">
                                <i class="fas fa-eye"></i> View Public
                            </a>
                        </div>
                        <a href="announcements.php" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Announcements
                        </a>
                    </div>
                </div>
                
                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo escape_html($error_message); ?>
                    </div>
                <?php endif; ?>
                
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Announcement Details</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="">
                                    <div class="mb-3">
                                        <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="title" name="title" 
                                               value="<?php echo escape_html($title); ?>" required maxlength="255">
                                        <div class="form-text">Maximum 255 characters</div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="content" class="form-label">Content <span class="text-danger">*</span></label>
                                        <textarea class="form-control" id="content" name="content" rows="10" required><?php echo escape_html($content); ?></textarea>
                                        <div class="form-text">You can use line breaks for formatting</div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="1" id="is_published" 
                                                   name="is_published" <?php echo $is_published ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="is_published">
                                                Published
                                            </label>
                                            <div class="form-text">Uncheck to save as draft</div>
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Update Announcement
                                        </button>
                                        <a href="announcements.php" class="btn btn-secondary">
                                            <i class="fas fa-times"></i> Cancel
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Announcement Info</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <strong>Author:</strong><br>
                                    <small class="text-muted"><?php echo escape_html($announcement_data['admin_username']); ?></small>
                                </div>
                                
                                <div class="mb-3">
                                    <strong>Created:</strong><br>
                                    <small class="text-muted"><?php echo format_date($announcement_data['created_at']); ?></small>
                                </div>
                                
                                <?php if ($announcement_data['updated_at'] !== $announcement_data['created_at']): ?>
                                <div class="mb-3">
                                    <strong>Last Updated:</strong><br>
                                    <small class="text-muted"><?php echo format_date($announcement_data['updated_at']); ?></small>
                                </div>
                                <?php endif; ?>
                                
                                <div class="mb-3">
                                    <strong>Current Status:</strong><br>
                                    <?php if ($announcement_data['is_published']): ?>
                                        <span class="badge bg-success">Published</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning">Draft</span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="alert alert-info">
                                    <small>
                                        <i class="fas fa-info-circle"></i>
                                        Changes will be visible immediately on the public site if published.
                                    </small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card mt-3">
                            <div class="card-header">
                                <h6 class="mb-0">Quick Actions</h6>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <a href="announcement_toggle.php?id=<?php echo $announcement_id; ?>" 
                                       class="btn btn-outline-<?php echo $is_published ? 'warning' : 'success'; ?> btn-sm">
                                        <i class="fas fa-<?php echo $is_published ? 'eye-slash' : 'eye'; ?>"></i>
                                        <?php echo $is_published ? 'Unpublish' : 'Publish'; ?>
                                    </a>
                                    <a href="announcement_delete.php?id=<?php echo $announcement_id; ?>" 
                                       class="btn btn-outline-danger btn-sm"
                                       onclick="return confirm('Are you sure you want to delete this announcement?')">
                                        <i class="fas fa-trash"></i> Delete
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Update status text when checkbox changes
        document.getElementById('is_published').addEventListener('change', function() {
            // Optional: Add visual feedback for status change
        });
    </script>
</body>
</html>

