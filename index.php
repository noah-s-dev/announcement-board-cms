<?php
/**
 * Public Homepage
 * Displays published announcements for public viewing
 */

require_once 'config/config.php';
require_once 'includes/functions.php';
require_once 'includes/Announcement.php';

$announcement = new Announcement();

// Handle pagination
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$per_page = ANNOUNCEMENTS_PER_PAGE;
$offset = ($page - 1) * $per_page;

// Handle search
$search = isset($_GET['search']) ? sanitize_input($_GET['search']) : '';

// Get published announcements
if (!empty($search)) {
    $announcements = $announcement->search($search, true); // Only published
    $total_announcements = count($announcements);
    $total_pages = 1; // No pagination for search results
} else {
    $announcements = $announcement->getPublished($per_page, $offset);
    $total_announcements = $announcement->getPublishedCount();
    $total_pages = ceil($total_announcements / $per_page);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?></title>
    <meta name="description" content="Stay updated with the latest announcements and news">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="assets/css/public.css" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <header class="bg-primary text-white py-4 mb-4">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="h2 mb-0">
                        <i class="fas fa-bullhorn"></i> <?php echo APP_NAME; ?>
                    </h1>
                    <p class="mb-0 opacity-75">Stay updated with the latest announcements</p>
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
        <!-- Search Bar -->
        <div class="row mb-4">
            <div class="col-lg-8 mx-auto">
                <div class="card shadow-sm fade-in">
                    <div class="card-body">
                        <form method="GET" action="">
                            <div class="input-group">
                                <input type="text" class="form-control form-control-lg" name="search" 
                                       placeholder="Search announcements..." value="<?php echo escape_html($search); ?>">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-search"></i> Search
                                </button>
                            </div>
                        </form>
                        <?php if (!empty($search)): ?>
                            <div class="mt-2">
                                <small class="text-muted">
                                    Searching for: <strong><?php echo escape_html($search); ?></strong>
                                    <a href="index.php" class="ms-2">Clear search</a>
                                </small>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Announcements -->
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <?php if ($announcements && count($announcements) > 0): ?>
                    <?php if (!empty($search)): ?>
                        <h3 class="mb-4">Search Results (<?php echo count($announcements); ?>)</h3>
                    <?php else: ?>
                        <h3 class="mb-4">Latest Announcements</h3>
                    <?php endif; ?>

                    <?php foreach ($announcements as $index => $ann): ?>
                        <article class="card mb-4 shadow-sm announcement-card slide-up" style="animation-delay: <?php echo $index * 0.1; ?>s;">
                            <div class="card-body">
                                <h4 class="card-title">
                                    <a href="announcement.php?id=<?php echo $ann['id']; ?>" class="text-decoration-none">
                                        <?php echo escape_html($ann['title']); ?>
                                    </a>
                                </h4>
                                
                                <div class="card-text">
                                    <p class="mb-3">
                                        <?php echo nl2br_safe(truncate_text($ann['content'], 300)); ?>
                                    </p>
                                    
                                    <?php if (strlen($ann['content']) > 300): ?>
                                        <a href="announcement.php?id=<?php echo $ann['id']; ?>" class="btn btn-outline-primary btn-sm hover-glow">
                                            Read More <i class="fas fa-arrow-right"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="card-footer bg-transparent border-0 px-0 pb-0">
                                    <small class="text-muted">
                                        <i class="fas fa-calendar"></i> <?php echo format_date($ann['created_at']); ?>
                                        <?php if (!empty($ann['admin_username'])): ?>
                                            <span class="ms-3">
                                                <i class="fas fa-user"></i> <?php echo escape_html($ann['admin_username']); ?>
                                            </span>
                                        <?php endif; ?>
                                    </small>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>

                    <!-- Pagination -->
                    <?php if (empty($search) && $total_pages > 1): ?>
                        <div class="mt-4">
                            <?php echo generate_pagination($page, $total_pages, 'index.php'); ?>
                        </div>
                    <?php endif; ?>

                <?php else: ?>
                    <div class="text-center py-5">
                        <?php if (!empty($search)): ?>
                            <i class="fas fa-search fa-4x text-muted mb-4"></i>
                            <h4 class="text-muted">No announcements found</h4>
                            <p class="text-muted">Try adjusting your search terms or browse all announcements.</p>
                            <a href="index.php" class="btn btn-primary">View All Announcements</a>
                        <?php else: ?>
                            <i class="fas fa-bullhorn fa-4x text-muted mb-4"></i>
                            <h4 class="text-muted">No announcements yet</h4>
                            <p class="text-muted">Check back later for updates and announcements.</p>
                        <?php endif; ?>
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

