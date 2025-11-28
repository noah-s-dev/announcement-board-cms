<?php
/**
 * Security Helper Class
 * Provides additional security features for the Announcement Board CMS
 */

class Security {
    
    /**
     * Generate CSRF token
     * @return string
     */
    public static function generateCSRFToken() {
        if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
            $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
        }
        return $_SESSION[CSRF_TOKEN_NAME];
    }
    
    /**
     * Verify CSRF token
     * @param string $token
     * @return bool
     */
    public static function verifyCSRFToken($token) {
        return isset($_SESSION[CSRF_TOKEN_NAME]) && hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
    }
    
    /**
     * Get CSRF token HTML input field
     * @return string
     */
    public static function getCSRFTokenField() {
        $token = self::generateCSRFToken();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
    }
    
    /**
     * Validate and sanitize input data
     * @param array $data
     * @param array $rules
     * @return array
     */
    public static function validateInput($data, $rules) {
        $errors = [];
        $sanitized = [];
        
        foreach ($rules as $field => $rule) {
            $value = $data[$field] ?? '';
            
            // Apply sanitization
            if (isset($rule['sanitize'])) {
                switch ($rule['sanitize']) {
                    case 'string':
                        $value = self::sanitizeString($value);
                        break;
                    case 'email':
                        $value = self::sanitizeEmail($value);
                        break;
                    case 'html':
                        $value = self::sanitizeHTML($value);
                        break;
                }
            }
            
            // Apply validation
            if (isset($rule['required']) && $rule['required'] && empty($value)) {
                $errors[$field] = ucfirst($field) . ' is required.';
                continue;
            }
            
            if (!empty($value)) {
                if (isset($rule['min_length']) && strlen($value) < $rule['min_length']) {
                    $errors[$field] = ucfirst($field) . ' must be at least ' . $rule['min_length'] . ' characters.';
                }
                
                if (isset($rule['max_length']) && strlen($value) > $rule['max_length']) {
                    $errors[$field] = ucfirst($field) . ' must be less than ' . $rule['max_length'] . ' characters.';
                }
                
                if (isset($rule['email']) && $rule['email'] && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[$field] = ucfirst($field) . ' must be a valid email address.';
                }
                
                if (isset($rule['pattern']) && !preg_match($rule['pattern'], $value)) {
                    $errors[$field] = $rule['pattern_error'] ?? ucfirst($field) . ' format is invalid.';
                }
            }
            
            $sanitized[$field] = $value;
        }
        
        return ['data' => $sanitized, 'errors' => $errors];
    }
    
    /**
     * Sanitize string input
     * @param string $input
     * @return string
     */
    public static function sanitizeString($input) {
        $input = trim($input);
        $input = stripslashes($input);
        return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Sanitize email input
     * @param string $input
     * @return string
     */
    public static function sanitizeEmail($input) {
        return filter_var(trim($input), FILTER_SANITIZE_EMAIL);
    }
    
    /**
     * Sanitize HTML input (allow basic formatting)
     * @param string $input
     * @return string
     */
    public static function sanitizeHTML($input) {
        // Allow only basic HTML tags
        $allowed_tags = '<p><br><strong><em><u><ol><ul><li><h1><h2><h3><h4><h5><h6>';
        return strip_tags(trim($input), $allowed_tags);
    }
    
    /**
     * Check for rate limiting
     * @param string $action
     * @param int $limit
     * @param int $window
     * @return bool
     */
    public static function checkRateLimit($action, $limit = 5, $window = 300) {
        $ip = self::getClientIP();
        $key = "rate_limit_{$action}_{$ip}";
        
        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = [];
        }
        
        $now = time();
        $attempts = $_SESSION[$key];
        
        // Remove old attempts outside the window
        $attempts = array_filter($attempts, function($timestamp) use ($now, $window) {
            return ($now - $timestamp) < $window;
        });
        
        if (count($attempts) >= $limit) {
            return false;
        }
        
        // Add current attempt
        $attempts[] = $now;
        $_SESSION[$key] = $attempts;
        
        return true;
    }
    
    /**
     * Get client IP address
     * @return string
     */
    public static function getClientIP() {
        $ip_keys = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'];
        
        foreach ($ip_keys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
    
    /**
     * Generate secure random password
     * @param int $length
     * @return string
     */
    public static function generatePassword($length = 12) {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
        $password = '';
        
        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[random_int(0, strlen($chars) - 1)];
        }
        
        return $password;
    }
    
    /**
     * Validate password strength
     * @param string $password
     * @return array
     */
    public static function validatePasswordStrength($password) {
        $errors = [];
        
        if (strlen($password) < PASSWORD_MIN_LENGTH) {
            $errors[] = 'Password must be at least ' . PASSWORD_MIN_LENGTH . ' characters long.';
        }
        
        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = 'Password must contain at least one lowercase letter.';
        }
        
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Password must contain at least one uppercase letter.';
        }
        
        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = 'Password must contain at least one number.';
        }
        
        return $errors;
    }
    
    /**
     * Log security event
     * @param string $event
     * @param array $data
     */
    public static function logSecurityEvent($event, $data = []) {
        $log_entry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'ip' => self::getClientIP(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'event' => $event,
            'data' => $data
        ];
        
        $log_file = '../logs/security.log';
        $log_dir = dirname($log_file);
        
        if (!is_dir($log_dir)) {
            mkdir($log_dir, 0755, true);
        }
        
        file_put_contents($log_file, json_encode($log_entry) . "\n", FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Check if request is from admin area
     * @return bool
     */
    public static function isAdminRequest() {
        return strpos($_SERVER['REQUEST_URI'], '/admin/') !== false;
    }
    
    /**
     * Prevent clickjacking attacks
     */
    public static function setSecurityHeaders() {
        // Prevent clickjacking
        header('X-Frame-Options: DENY');
        
        // Prevent MIME type sniffing
        header('X-Content-Type-Options: nosniff');
        
        // Enable XSS protection
        header('X-XSS-Protection: 1; mode=block');
        
        // Referrer policy
        header('Referrer-Policy: strict-origin-when-cross-origin');
        
        // Content Security Policy (basic)
        if (self::isAdminRequest()) {
            header("Content-Security-Policy: default-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; img-src 'self' data: https:;");
        }
    }
    
    /**
     * Clean old session data
     */
    public static function cleanOldSessions() {
        // Clean rate limit data older than 1 hour
        $now = time();
        foreach ($_SESSION as $key => $value) {
            if (strpos($key, 'rate_limit_') === 0 && is_array($value)) {
                $_SESSION[$key] = array_filter($value, function($timestamp) use ($now) {
                    return ($now - $timestamp) < 3600;
                });
            }
        }
    }
}
?>

