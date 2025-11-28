<?php
/**
 * Announcements Listing Page
 * Shows all announcements for admin management
 */

require_once '../config/config.php';
require_once '../includes/functions.php';
require_once '../includes/Announcement.php';

// Check if admin is logged in
if (!is_admin_logged_in()) {
    redirect('login.php');
}

$announcement = new Announcement();

// Handle pagination
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$per_page = ANNOUNCEMENTS_PER_PAGE;
$offset = ($page - 1) * $per_page;

// Handle search
$search = isset($_GET['search']) ? sanitize_input($_GET['search']) : '';

// Get announcements
if (!empty($search)) {
    $announcements = $announcement->search($search, false); // Include drafts in admin search
    $total_announcements = count($announcements);
    $total_pages = 1; // No pagination for search results
} else {
    $announcements = $announcement->getAll($per_page, $offset);
    $total_announcements = $announcement->getTotalCount();
    $total_pages = ceil($total_announcements / $per_page);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Announcements - <?php echo APP_NAME; ?></title>
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
                    <h1 class="h2">Manage Announcements</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <a href="announcement_create.php" class="btn btn-primary">
                            <i class="fas fa-plus"></i> New Announcement
                        </a>
                    </div>
                </div>
                
                <?php echo display_flash_message(); ?>
                
                <!-- Search and Filters -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" action="" class="row g-3">
                            <div class="col-md-8">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="search" 
                                           placeholder="Search announcements..." value="<?php echo escape_html($search); ?>">
                                    <button class="btn btn-outline-secondary" type="submit">
                                        <i class="fas fa-search"></i> Search
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <?php if (!empty($search)): ?>
                                    <a href="announcements.php" class="btn btn-outline-secondary">
                                        <i class="fas fa-times"></i> Clear Search
                                    </a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Announcements Table -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <?php if (!empty($search)): ?>
                                Search Results for "<?php echo escape_html($search); ?>"
                            <?php else: ?>
                                All Announcements (<?php echo $total_announcements; ?>)
                            <?php endif; ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if ($announcements && count($announcements) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Status</th>
                                            <th>Created</th>
                                            <th>Updated</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($announcements as $ann): ?>
                                            <tr>
                                                <td>
                                                    <strong><?php echo escape_html($ann['title']); ?></strong>
                                                    <br>
                                                    <small class="text-muted">
                                                        <?php echo truncate_text(strip_tags($ann['content']), 100); ?>
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
                                                    <small>
                                                        <?php echo format_date($ann['created_at'], 'M j, Y'); ?><br>
                                                        <?php echo format_date($ann['created_at'], 'g:i A'); ?>
                                                    </small>
                                                </td>
                                                <td>
                                                    <small>
                                                        <?php if ($ann['updated_at'] !== $ann['created_at']): ?>
                                                            <?php echo format_date($ann['updated_at'], 'M j, Y'); ?><br>
                                                            <?php echo format_date($ann['updated_at'], 'g:i A'); ?>
                                                        <?php else: ?>
                                                            <span class="text-muted">Not updated</span>
                                                        <?php endif; ?>
                                                    </small>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="../announcement.php?id=<?php echo $ann['id']; ?>" 
                                                           class="btn btn-outline-info" title="View" target="_blank">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="announcement_edit.php?id=<?php echo $ann['id']; ?>" 
                                                           class="btn btn-outline-primary" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <a href="announcement_toggle.php?id=<?php echo $ann['id']; ?>" 
                                                           class="btn btn-outline-<?php echo $ann['is_published'] ? 'warning' : 'success'; ?>" 
                                                           title="<?php echo $ann['is_published'] ? 'Unpublish' : 'Publish'; ?>">
                                                            <i class="fas fa-<?php echo $ann['is_published'] ? 'eye-slash' : 'eye'; ?>"></i>
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
                            
                            <?php if (empty($search) && $total_pages > 1): ?>
                                <div class="mt-4">
                                    <?php echo generate_pagination($page, $total_pages, 'announcements.php'); ?>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <?php if (!empty($search)): ?>
                                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No announcements found</h5>
                                    <p class="text-muted">Try adjusting your search terms.</p>
                                    <a href="announcements.php" class="btn btn-outline-secondary">View All Announcements</a>
                                <?php else: ?>
                                    <i class="fas fa-bullhorn fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No announcements yet</h5>
                                    <p class="text-muted">Create your first announcement to get started.</p>
                                    <a href="announcement_create.php" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Create Announcement
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

