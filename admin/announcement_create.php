<?php
/**
 * Create Announcement Page
 * Allows admins to create new announcements
 */

require_once '../config/config.php';
require_once '../includes/functions.php';
require_once '../includes/Announcement.php';

// Check if admin is logged in
if (!is_admin_logged_in()) {
    redirect('login.php');
}

$error_message = '';
$success_message = '';
$title = '';
$content = '';
$is_published = true;

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
        $announcement = new Announcement();
        $announcement_id = $announcement->create($title, $content, get_current_admin_id(), $is_published);
        
        if ($announcement_id) {
            set_flash_message('Announcement created successfully!', 'success');
            redirect('announcements.php');
        } else {
            $error_message = 'Failed to create announcement. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Announcement - <?php echo APP_NAME; ?></title>
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
                    <h1 class="h2">Create New Announcement</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
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
                                                Publish immediately
                                            </label>
                                            <div class="form-text">Uncheck to save as draft</div>
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Create Announcement
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
                                <h6 class="mb-0">Publishing Options</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <strong>Author:</strong><br>
                                    <small class="text-muted"><?php echo escape_html(get_current_admin_username()); ?></small>
                                </div>
                                
                                <div class="mb-3">
                                    <strong>Status:</strong><br>
                                    <small class="text-muted" id="status-text">
                                        <?php echo $is_published ? 'Will be published' : 'Will be saved as draft'; ?>
                                    </small>
                                </div>
                                
                                <div class="alert alert-info">
                                    <small>
                                        <i class="fas fa-info-circle"></i>
                                        Published announcements will be visible to all visitors on the public site.
                                    </small>
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
            const statusText = document.getElementById('status-text');
            statusText.textContent = this.checked ? 'Will be published' : 'Will be saved as draft';
        });
    </script>
</body>
</html>

