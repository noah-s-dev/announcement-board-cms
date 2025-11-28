<?php
/**
 * Admin Dashboard
 * Main admin interface for managing announcements
 */

require_once '../config/config.php';
require_once '../includes/functions.php';
require_once '../includes/Admin.php';
require_once '../includes/Announcement.php';

// Check if admin is logged in
if (!is_admin_logged_in()) {
    redirect('login.php');
}

$announcement = new Announcement();

// Get statistics
$total_announcements = $announcement->getTotalCount();
$published_announcements = $announcement->getPublishedCount();
$draft_announcements = $total_announcements - $published_announcements;

// Get recent announcements
$recent_announcements = $announcement->getAll(5);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - <?php echo APP_NAME; ?></title>
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
                    <h1 class="h2">Dashboard</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <a href="announcement_create.php" class="btn btn-sm btn-primary">
                                <i class="fas fa-plus"></i> New Announcement
                            </a>
                        </div>
                    </div>
                </div>
                
                <?php echo display_flash_message(); ?>
                
                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card text-white bg-primary">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h5 class="card-title">Total Announcements</h5>
                                        <h2 class="mb-0"><?php echo $total_announcements; ?></h2>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-bullhorn fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card text-white bg-success">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h5 class="card-title">Published</h5>
                                        <h2 class="mb-0"><?php echo $published_announcements; ?></h2>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-check-circle fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card text-white bg-warning">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h5 class="card-title">Drafts</h5>
                                        <h2 class="mb-0"><?php echo $draft_announcements; ?></h2>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-edit fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Announcements -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Recent Announcements</h5>
                        <a href="announcements.php" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    <div class="card-body">
                        <?php if ($recent_announcements && count($recent_announcements) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Status</th>
                                            <th>Created</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recent_announcements as $ann): ?>
                                            <tr>
                                                <td>
                                                    <strong><?php echo escape_html($ann['title']); ?></strong>
                                                    <br>
                                                    <small class="text-muted">
                                                        <?php echo truncate_text(strip_tags($ann['content']), 80); ?>
                                                    </small>
                                                </td>
                                                <td>
                                                    <?php if ($ann['is_published']): ?>
                                                        <span class="badge bg-success">Published</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-warning">Draft</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <small><?php echo format_date($ann['created_at']); ?></small>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="announcement_edit.php?id=<?php echo $ann['id']; ?>" 
                                                           class="btn btn-outline-primary" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <a href="announcement_delete.php?id=<?php echo $ann['id']; ?>" 
                                                           class="btn btn-outline-danger" title="Delete"
                                                           onclick="return confirm('Are you sure you want to delete this announcement?')">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="fas fa-bullhorn fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No announcements yet</h5>
                                <p class="text-muted">Create your first announcement to get started.</p>
                                <a href="announcement_create.php" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Create Announcement
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/admin.js"></script>
</body>
</html>

