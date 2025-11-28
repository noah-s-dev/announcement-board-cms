<?php
/**
 * Admin Setup Script
 * Creates the first admin account for the Announcement Board CMS
 * This file should be deleted after first use for security
 */

require_once '../config/config.php';
require_once '../includes/functions.php';
require_once '../includes/Admin.php';

// Check if already has admin users
$admin = new Admin();
$admin_count = $admin->getAdminCount();

if ($admin_count > 0) {
    die('Admin setup already completed. This script should be deleted for security.');
}

$error_message = '';
$success_message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize_input($_POST['username'] ?? '');
    $email = sanitize_input($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validation
    if (empty($username) || empty($email) || empty($password)) {
        $error_message = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Please enter a valid email address.';
    } elseif (strlen($password) < 8) {
        $error_message = 'Password must be at least 8 characters long.';
    } elseif ($password !== $confirm_password) {
        $error_message = 'Passwords do not match.';
    } elseif (strlen($username) < 3) {
        $error_message = 'Username must be at least 3 characters long.';
    } else {
        // Create admin account
        if ($admin->create($username, $email, $password)) {
            $success_message = 'Admin account created successfully! You can now log in.';
            
            // Log the setup
            Security::logSecurityEvent('admin_setup_completed', [
                'username' => $username,
                'email' => $email,
                'ip' => Security::getClientIP()
            ]);
        } else {
            $error_message = 'Failed to create admin account. Username or email may already exist.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Setup - <?php echo APP_NAME; ?></title>
    <meta name="description" content="Initial admin setup for <?php echo APP_NAME; ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            position: relative;
            overflow: hidden;
        }
        
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/><circle cx="50" cy="10" r="0.5" fill="white" opacity="0.1"/><circle cx="10" cy="60" r="0.5" fill="white" opacity="0.1"/><circle cx="90" cy="40" r="0.5" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.3;
        }
        
        .setup-container {
            position: relative;
            z-index: 1;
        }
        
        .setup-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 1rem;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
        }
        
        .setup-header {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border-radius: 1rem 1rem 0 0;
            padding: 2rem;
            text-align: center;
            color: white;
        }
        
        .setup-header h1 {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
        }
        
        .setup-header p {
            opacity: 0.9;
            margin-bottom: 0;
        }
        
        .setup-body {
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
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
            transform: translateY(-1px);
        }
        
        .form-floating label {
            color: #64748b;
            font-weight: 500;
        }
        
        .btn-setup {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border: none;
            border-radius: 0.75rem;
            padding: 1rem 2rem;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .btn-setup::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }
        
        .btn-setup:hover::before {
            left: 100%;
        }
        
        .btn-setup:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(16, 185, 129, 0.3);
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
        
        .security-notice {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border: 1px solid #f59e0b;
            border-radius: 0.75rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .security-notice h5 {
            color: #92400e;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .security-notice p {
            color: #92400e;
            margin-bottom: 0;
            font-size: 0.95rem;
        }
        
        .password-strength {
            margin-top: 0.5rem;
            font-size: 0.875rem;
        }
        
        .strength-weak { color: #dc2626; }
        .strength-medium { color: #f59e0b; }
        .strength-strong { color: #16a34a; }
        
        @media (max-width: 768px) {
            .setup-card {
                margin: 1rem;
            }
            
            .setup-header {
                padding: 1.5rem;
            }
            
            .setup-header h1 {
                font-size: 1.5rem;
            }
            
            .setup-body {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="setup-container">
        <div class="container">
            <div class="row justify-content-center align-items-center min-vh-100">
                <div class="col-md-6 col-lg-5 col-xl-4">
                    <div class="setup-card">
                        <div class="setup-header">
                            <h1><i class="fas fa-user-shield"></i> Admin Setup</h1>
                            <p>Create Your First Admin Account</p>
                        </div>
                        
                        <div class="setup-body">
                            <div class="security-notice">
                                <h5><i class="fas fa-exclamation-triangle"></i> Security Notice</h5>
                                <p>This script should be deleted after creating your admin account for security reasons.</p>
                            </div>
                            
                            <?php if (!empty($error_message)): ?>
                                <div class="alert alert-danger" role="alert">
                                    <i class="fas fa-exclamation-triangle"></i> <?php echo escape_html($error_message); ?>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($success_message)): ?>
                                <div class="alert alert-success" role="alert">
                                    <i class="fas fa-check-circle"></i> <?php echo escape_html($success_message); ?>
                                    <div class="mt-2">
                                        <a href="login.php" class="btn btn-sm btn-success">
                                            <i class="fas fa-sign-in-alt"></i> Go to Login
                                        </a>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <form method="POST" action="" id="setupForm">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="username" name="username" 
                                           placeholder="Username" 
                                           value="<?php echo escape_html($username ?? ''); ?>" required>
                                    <label for="username">Username</label>
                                </div>
                                
                                <div class="form-floating">
                                    <input type="email" class="form-control" id="email" name="email" 
                                           placeholder="Email" 
                                           value="<?php echo escape_html($email ?? ''); ?>" required>
                                    <label for="email">Email Address</label>
                                </div>
                                
                                <div class="form-floating">
                                    <input type="password" class="form-control" id="password" name="password" 
                                           placeholder="Password" required>
                                    <label for="password">Password</label>
                                    <div class="password-strength" id="passwordStrength"></div>
                                </div>
                                
                                <div class="form-floating">
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                                           placeholder="Confirm Password" required>
                                    <label for="confirm_password">Confirm Password</label>
                                </div>
                                
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-setup" id="setupBtn">
                                        <span class="btn-text">Create Admin Account</span>
                                        <span class="btn-loading" style="display: none;">
                                            <i class="fas fa-spinner fa-spin"></i> Creating Account...
                                        </span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <div class="text-center mt-4">
                        <a href="../index.php" class="btn btn-outline-light">
                            <i class="fas fa-arrow-left"></i> Back to Public Site
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const setupForm = document.getElementById('setupForm');
            const setupBtn = document.getElementById('setupBtn');
            const btnText = setupBtn.querySelector('.btn-text');
            const btnLoading = setupBtn.querySelector('.btn-loading');
            const passwordInput = document.getElementById('password');
            const confirmPasswordInput = document.getElementById('confirm_password');
            const passwordStrength = document.getElementById('passwordStrength');
            
            // Password strength checker
            function checkPasswordStrength(password) {
                let strength = 0;
                let feedback = [];
                
                if (password.length >= 8) strength++;
                else feedback.push('At least 8 characters');
                
                if (/[a-z]/.test(password)) strength++;
                else feedback.push('Lowercase letter');
                
                if (/[A-Z]/.test(password)) strength++;
                else feedback.push('Uppercase letter');
                
                if (/[0-9]/.test(password)) strength++;
                else feedback.push('Number');
                
                if (/[^A-Za-z0-9]/.test(password)) strength++;
                else feedback.push('Special character');
                
                let strengthText = '';
                let strengthClass = '';
                
                if (strength < 3) {
                    strengthText = 'Weak';
                    strengthClass = 'strength-weak';
                } else if (strength < 5) {
                    strengthText = 'Medium';
                    strengthClass = 'strength-medium';
                } else {
                    strengthText = 'Strong';
                    strengthClass = 'strength-strong';
                }
                
                passwordStrength.textContent = `Strength: ${strengthText}`;
                passwordStrength.className = `password-strength ${strengthClass}`;
            }
            
            passwordInput.addEventListener('input', function() {
                checkPasswordStrength(this.value);
            });
            
            // Form submission with loading state
            setupForm.addEventListener('submit', function(e) {
                if (passwordInput.value !== confirmPasswordInput.value) {
                    e.preventDefault();
                    alert('Passwords do not match!');
                    return;
                }
                
                btnText.style.display = 'none';
                btnLoading.style.display = 'inline-block';
                setupBtn.disabled = true;
                
                // Re-enable after 5 seconds in case of errors
                setTimeout(() => {
                    btnText.style.display = 'inline-block';
                    btnLoading.style.display = 'none';
                    setupBtn.disabled = false;
                }, 5000);
            });
            
            // Auto-focus username field
            document.getElementById('username').focus();
        });
    </script>
</body>
</html> 