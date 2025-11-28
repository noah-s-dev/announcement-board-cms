<?php
/**
 * Admin Login Page
 * Modern and secure admin authentication for the Announcement Board CMS
 */

require_once '../config/config.php';
require_once '../includes/functions.php';
require_once '../includes/Admin.php';

// Redirect if already logged in
if (is_admin_logged_in()) {
    redirect('dashboard.php');
}

$error_message = '';
$success_message = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check rate limiting
    if (!Security::checkRateLimit('login', 5, 300)) {
        $error_message = 'Too many login attempts. Please try again in 5 minutes.';
        Security::logSecurityEvent('login_rate_limit_exceeded', [
            'ip' => Security::getClientIP(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? ''
        ]);
    } else {
        // Verify CSRF token
        $csrf_token = $_POST['csrf_token'] ?? '';
        if (!Security::verifyCSRFToken($csrf_token)) {
            $error_message = 'Invalid security token. Please try again.';
            Security::logSecurityEvent('csrf_token_invalid', [
                'action' => 'login',
                'ip' => Security::getClientIP()
            ]);
        } else {
            $username = sanitize_input($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';
            
            if (empty($username) || empty($password)) {
                $error_message = 'Please enter both username and password.';
            } else {
                $admin = new Admin();
                $admin_data = $admin->authenticate($username, $password);
                
                if ($admin_data) {
                    // Set session variables
                    $_SESSION['admin_id'] = $admin_data['id'];
                    $_SESSION['admin_username'] = $admin_data['username'];
                    $_SESSION['admin_email'] = $admin_data['email'];
                    
                    // Log successful login
                    Security::logSecurityEvent('admin_login_success', [
                        'admin_id' => $admin_data['id'],
                        'username' => $admin_data['username']
                    ]);
                    
                    // Redirect to dashboard
                    redirect('dashboard.php');
                } else {
                    $error_message = 'Invalid username or password.';
                    Security::logSecurityEvent('admin_login_failed', [
                        'username' => $username,
                        'ip' => Security::getClientIP()
                    ]);
                }
            }
        }
    }
}

// Check if this is first-time setup (no admin users exist)
$admin = new Admin();
$admin_count = $admin->getAdminCount();
$is_first_time = $admin_count === 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - <?php echo APP_NAME; ?></title>
    <meta name="description" content="Secure admin login for <?php echo APP_NAME; ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="../assets/css/admin.css" rel="stylesheet">
    <style>
        /* Login-specific styles */
        .login-container {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            position: relative;
            overflow: hidden;
        }
        
        .login-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/><circle cx="50" cy="10" r="0.5" fill="white" opacity="0.1"/><circle cx="10" cy="60" r="0.5" fill="white" opacity="0.1"/><circle cx="90" cy="40" r="0.5" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.3;
        }
        
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 1rem;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
            position: relative;
            z-index: 1;
        }
        
        .login-header {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            border-radius: 1rem 1rem 0 0;
            padding: 2rem;
            text-align: center;
            color: white;
        }
        
        .login-header h1 {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
        }
        
        .login-header p {
            opacity: 0.9;
            margin-bottom: 0;
        }
        
        .login-body {
            padding: 2rem;
        }
        
        .form-floating {
            margin-bottom: 1.5rem;
        }
        
        .form-floating .form-control {
            border: 2px solid #e2e8f0;
            border-radius: 0.75rem;
            padding: 1rem 0.75rem;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .form-floating .form-control:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
            transform: translateY(-1px);
        }
        
        .form-floating label {
            color: #64748b;
            font-weight: 500;
        }
        
        .btn-login {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            border: none;
            border-radius: 0.75rem;
            padding: 1rem 2rem;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }
        
        .btn-login:hover::before {
            left: 100%;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(99, 102, 241, 0.3);
        }
        
        .btn-back {
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(255, 255, 255, 0.2);
            color: white;
            border-radius: 0.75rem;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }
        
        .btn-back:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.3);
            color: white;
            transform: translateY(-1px);
        }
        
        .setup-notice {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border: 1px solid #f59e0b;
            border-radius: 0.75rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .setup-notice h5 {
            color: #92400e;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .setup-notice p {
            color: #92400e;
            margin-bottom: 0;
            font-size: 0.95rem;
        }
        
        .security-info {
            background: rgba(99, 102, 241, 0.05);
            border: 1px solid rgba(99, 102, 241, 0.1);
            border-radius: 0.75rem;
            padding: 1rem;
            margin-top: 1.5rem;
        }
        
        .security-info h6 {
            color: #6366f1;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .security-info ul {
            margin-bottom: 0;
            padding-left: 1.25rem;
        }
        
        .security-info li {
            color: #64748b;
            font-size: 0.875rem;
            margin-bottom: 0.25rem;
        }
        
        .alert {
            border: none;
            border-radius: 0.75rem;
            font-weight: 500;
        }
        
        .alert-danger {
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            color: #dc2626;
            border-left: 4px solid #dc2626;
        }
        
        .alert-success {
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            color: #16a34a;
            border-left: 4px solid #16a34a;
        }
        
        .floating-shapes {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            pointer-events: none;
        }
        
        .shape {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }
        
        .shape:nth-child(1) {
            width: 80px;
            height: 80px;
            top: 20%;
            left: 10%;
            animation-delay: 0s;
        }
        
        .shape:nth-child(2) {
            width: 120px;
            height: 120px;
            top: 60%;
            right: 10%;
            animation-delay: 2s;
        }
        
        .shape:nth-child(3) {
            width: 60px;
            height: 60px;
            bottom: 20%;
            left: 20%;
            animation-delay: 4s;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }
        
        @media (max-width: 768px) {
            .login-card {
                margin: 1rem;
            }
            
            .login-header {
                padding: 1.5rem;
            }
            
            .login-header h1 {
                font-size: 1.5rem;
            }
            
            .login-body {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Floating background shapes -->
        <div class="floating-shapes">
            <div class="shape"></div>
            <div class="shape"></div>
            <div class="shape"></div>
        </div>
        
        <div class="container">
            <div class="row justify-content-center align-items-center min-vh-100">
                <div class="col-md-6 col-lg-5 col-xl-4">
                    <div class="login-card">
                        <div class="login-header">
                            <h1><i class="fas fa-shield-alt"></i> Admin Login</h1>
                            <p><?php echo APP_NAME; ?> Management</p>
                        </div>
                        
                        <div class="login-body">
                            <?php if ($is_first_time): ?>
                                <div class="setup-notice">
                                    <h5><i class="fas fa-info-circle"></i> First Time Setup</h5>
                                    <p>No admin users found. Please create your first admin account by running the database setup script or manually adding an admin user to the database.</p>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($error_message)): ?>
                                <div class="alert alert-danger" role="alert">
                                    <i class="fas fa-exclamation-triangle"></i> <?php echo escape_html($error_message); ?>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($success_message)): ?>
                                <div class="alert alert-success" role="alert">
                                    <i class="fas fa-check-circle"></i> <?php echo escape_html($success_message); ?>
                                </div>
                            <?php endif; ?>
                            
                            <form method="POST" action="" id="loginForm">
                                <?php echo Security::getCSRFTokenField(); ?>
                                
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="username" name="username" 
                                           placeholder="Username or Email" 
                                           value="<?php echo escape_html($username ?? ''); ?>" required>
                                    <label for="username">Username or Email</label>
                                </div>
                                
                                <div class="form-floating">
                                    <input type="password" class="form-control" id="password" name="password" 
                                           placeholder="Password" required>
                                    <label for="password">Password</label>
                                </div>
                                
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-login" id="loginBtn">
                                        <span class="btn-text">Sign In</span>
                                        <span class="btn-loading" style="display: none;">
                                            <i class="fas fa-spinner fa-spin"></i> Signing In...
                                        </span>
                                    </button>
                                </div>
                            </form>
                            
                            <div class="security-info">
                                <h6><i class="fas fa-lock"></i> Security Features</h6>
                                <ul>
                                    <li>Rate limiting protection</li>
                                    <li>CSRF token validation</li>
                                    <li>Secure password hashing</li>
                                    <li>Session management</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-center mt-4">
                        <a href="../index.php" class="btn btn-back">
                            <i class="fas fa-arrow-left"></i> Back to Public Site
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Enhanced login form interactions
        document.addEventListener('DOMContentLoaded', function() {
            const loginForm = document.getElementById('loginForm');
            const loginBtn = document.getElementById('loginBtn');
            const btnText = loginBtn.querySelector('.btn-text');
            const btnLoading = loginBtn.querySelector('.btn-loading');
            
            // Form submission with loading state
            loginForm.addEventListener('submit', function(e) {
                btnText.style.display = 'none';
                btnLoading.style.display = 'inline-block';
                loginBtn.disabled = true;
                
                // Re-enable after 5 seconds in case of errors
                setTimeout(() => {
                    btnText.style.display = 'inline-block';
                    btnLoading.style.display = 'none';
                    loginBtn.disabled = false;
                }, 5000);
            });
            
            // Password visibility toggle (optional enhancement)
            const passwordInput = document.getElementById('password');
            const togglePassword = document.createElement('button');
            togglePassword.type = 'button';
            togglePassword.className = 'btn btn-link position-absolute';
            togglePassword.style.cssText = 'right: 10px; top: 50%; transform: translateY(-50%); z-index: 10; color: #64748b;';
            togglePassword.innerHTML = '<i class="fas fa-eye"></i>';
            
            passwordInput.parentElement.style.position = 'relative';
            passwordInput.parentElement.appendChild(togglePassword);
            
            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                this.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
            });
            
            // Auto-focus username field
            document.getElementById('username').focus();
            
            // Keyboard shortcuts
            document.addEventListener('keydown', function(e) {
                // Enter to submit form
                if (e.key === 'Enter' && document.activeElement === passwordInput) {
                    loginForm.submit();
                }
                
                // Escape to clear form
                if (e.key === 'Escape') {
                    loginForm.reset();
                    document.getElementById('username').focus();
                }
            });
        });
    </script>
</body>
</html>

