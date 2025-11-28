<?php
/**
 * Admin Sidebar Include
 * Navigation sidebar for admin pages
 */

// Get current page name for active menu highlighting
$current_page = basename($_SERVER['PHP_SELF']);
?>
<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
    <div class="position-sticky pt-3">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php echo ($current_page === 'dashboard.php') ? 'active' : ''; ?>" 
                   href="dashboard.php">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?php echo ($current_page === 'announcements.php') ? 'active' : ''; ?>" 
                   href="announcements.php">
                    <i class="fas fa-list"></i> All Announcements
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?php echo ($current_page === 'announcement_create.php') ? 'active' : ''; ?>" 
                   href="announcement_create.php">
                    <i class="fas fa-plus"></i> New Announcement
                </a>
            </li>
        </ul>
        
        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
            <span>Settings</span>
        </h6>
        
        <ul class="nav flex-column mb-2">
            <li class="nav-item">
                <a class="nav-link <?php echo ($current_page === 'profile.php') ? 'active' : ''; ?>" 
                   href="profile.php">
                    <i class="fas fa-user-edit"></i> Profile
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link" href="../index.php" target="_blank">
                    <i class="fas fa-external-link-alt"></i> View Public Site
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link" href="logout.php">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </li>
        </ul>
    </div>
</nav>

