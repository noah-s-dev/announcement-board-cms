<?php
/**
 * Announcement Detail Page
 * Displays a single announcement in full detail
 */

require_once 'config/config.php';
require_once 'includes/functions.php';
require_once 'includes/Announcement.php';

// Get announcement ID
$announcement_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($announcement_id <= 0) {
    header("HTTP/1.0 404 Not Found");
    include '404.php';
    exit;
}

$announcement = new Announcement();
$announcement_data = $announcement->getPublishedById($announcement_id);

if (!$announcement_data) {
    header("HTTP/1.0 404 Not Found");
    include '404.php';
    exit;
}

// Get other recent announcements for sidebar
$recent_announcements = $announcement->getPublished(5);
// Remove current announcement from recent list
$recent_announcements = array_filter($recent_announcements, function($ann) use ($announcement_id) {
    return $ann['id'] != $announcement_id;
});
$recent_announcements = array_slice($recent_announcements, 0, 4);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo escape_html($announcement_data['title']); ?> - <?php echo APP_NAME; ?></title>
    <meta name="description" content="<?php echo escape_html(truncate_text(strip_tags($announcement_data['content']), 160, '...')); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="assets/css/public.css" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <header class="bg-primary text-white py-3 mb-4">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="h3 mb-0">
                        <a href="index.php" class="text-white text-decoration-none">
                            <i class="fas fa-bullhorn"></i> <?php echo APP_NAME; ?>
                        </a>
                    </h1>
                </div>
                <div class="col-md-4 text-md-end">
                    <a href="admin/login.php" class="btn btn-light btn-sm">
                        <i class="fas fa-user-shield"></i> Admin Login
                    </a>
                </div>
            </div>
        </div>
    </header>

    <div class="container">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="index.php"><i class="fas fa-home"></i> Home</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">
                    <?php echo escape_html(truncate_text($announcement_data['title'], 50)); ?>
                </li>
            </ol>
        </nav>

        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <article class="card shadow-sm fade-in">
                    <div class="card-body">
                        <header class="mb-4">
                            <h1 class="display-6 mb-3"><?php echo escape_html($announcement_data['title']); ?></h1>
                            
                            <div class="d-flex flex-wrap align-items-center text-muted mb-3">
                                <span class="me-4">
                                    <i class="fas fa-calendar"></i> 
                                    <?php echo format_date($announcement_data['created_at']); ?>
                                </span>
                                
                                <?php if (!empty($announcement_data['admin_username'])): ?>
                                    <span class="me-4">
                                        <i class="fas fa-user"></i> 
                                        <?php echo escape_html($announcement_data['admin_username']); ?>
                                    </span>
                                <?php endif; ?>
                                
                                <?php if ($announcement_data['updated_at'] !== $announcement_data['created_at']): ?>
                                    <span>
                                        <i class="fas fa-edit"></i> 
                                        Updated <?php echo format_date($announcement_data['updated_at']); ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </header>

                        <div class="announcement-content">
                            <?php echo nl2br_safe($announcement_data['content']); ?>
                        </div>
                    </div>
                </article>

                <!-- Navigation -->
                <div class="mt-4">
                    <a href="index.php" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left"></i> Back to All Announcements
                    </a>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Search -->
                <div class="card mb-4 slide-up">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-search"></i> Search</h5>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="index.php">
                            <div class="input-group">
                                <input type="text" class="form-control" name="search" 
                                       placeholder="Search announcements...">
                                <button class="btn btn-outline-primary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Recent Announcements -->
                <?php if (!empty($recent_announcements)): ?>
                    <div class="card slide-up" style="animation-delay: 0.2s;">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-clock"></i> Recent Announcements</h5>
                        </div>
                        <div class="card-body">
                            <?php foreach ($recent_announcements as $recent): ?>
                                <div class="mb-3 pb-3 <?php echo ($recent !== end($recent_announcements)) ? 'border-bottom' : ''; ?>">
                                    <h6 class="mb-1">
                                        <a href="announcement.php?id=<?php echo $recent['id']; ?>" class="text-decoration-none">
                                            <?php echo escape_html($recent['title']); ?>
                                        </a>
                                    </h6>
                                    <small class="text-muted">
                                        <i class="fas fa-calendar"></i> 
                                        <?php echo format_date($recent['created_at'], 'M j, Y'); ?>
                                    </small>
                                    <p class="small text-muted mt-1 mb-0">
                                        <?php echo truncate_text(strip_tags($recent['content']), 80); ?>
                                    </p>
                                </div>
                            <?php endforeach; ?>
                            
                            <div class="text-center">
                                <a href="index.php" class="btn btn-sm btn-outline-primary hover-glow">
                                    View All Announcements
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-light mt-5 py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-0 text-muted">
                        &copy; <?php echo date('Y'); ?> <?php echo APP_NAME; ?>. All rights reserved.
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <small class="text-muted">
                        Powered by Announcement Board CMS v<?php echo APP_VERSION; ?>
                    </small>
                </div>
            </div>
        </div>
    </footer>

    <!-- Copyright Footer -->
    <div class="copyright-footer">
        <div class="container">
            <div class="text-center my-2">
                <div>
                    <span>© 2025 . </span>
                    <span class="text-primary">Developed by </span>
                    <a href="https://rivertheme.com" class="fw-semibold text-decoration-none" target="_blank" rel="noopener">RiverTheme</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/public.js"></script>
</body>
</html>

