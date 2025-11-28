<?php
/**
 * 404 Error Page
 * Displayed when requested content is not found
 */

require_once 'config/config.php';
require_once 'includes/functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Found - <?php echo APP_NAME; ?></title>
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
        <div class="row justify-content-center">
            <div class="col-lg-6 text-center">
                <div class="py-5 fade-in">
                    <i class="fas fa-exclamation-triangle fa-5x text-warning mb-4 bounce-in"></i>
                    <h1 class="display-4 mb-3">404</h1>
                    <h2 class="h4 mb-3">Page Not Found</h2>
                    <p class="lead text-muted mb-4">
                        The announcement or page you're looking for doesn't exist or may have been removed.
                    </p>
                    
                    <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
                        <a href="index.php" class="btn btn-primary hover-glow">
                            <i class="fas fa-home"></i> Go to Homepage
                        </a>
                        <a href="javascript:history.back()" class="btn btn-outline-secondary hover-glow">
                            <i class="fas fa-arrow-left"></i> Go Back
                        </a>
                    </div>
                </div>
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

