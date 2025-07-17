<?php
// Prevent direct access to the file
defined('SITE_ROOT') or die('Direct access not allowed');

// Environment Configuration
define('ENVIRONMENT', 'development'); // Can be 'development', 'staging', or 'production'
define('DEBUG_MODE', true); // Set to false in production

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'travel_management');
define('DB_PORT', 3306);

// Site-wide Configuration
define('SITE_URL', 'http://localhost/raju/trae_travelproject/travel&tourism/');
define('BASE_PROJECT_URL', SITE_URL . 'user/');

// Security Configuration
define('CSRF_TOKEN_NAME', 'travel_csrf_token');
define('PASSWORD_HASH_COST', 12); // bcrypt cost factor

// File Upload Configuration
define('UPLOAD_DIR', __DIR__ . '/../../uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_FILE_TYPES', ['jpg', 'jpeg', 'png', 'gif', 'pdf']);

// Email Configuration
define('SMTP_HOST', 'localhost');
define('SMTP_PORT', 25);
define('SMTP_USER', '');
define('SMTP_PASS', '');
define('SITE_EMAIL', 'noreply@traetravel.com');

// Logging Configuration
define('LOG_DIR', __DIR__ . '/../../logs/');

// Error Handling
if (ENVIRONMENT === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Create log directory if it doesn't exist
if (!is_dir(LOG_DIR)) {
    mkdir(LOG_DIR, 0755, true);
}

// Create uploads directory if it doesn't exist
if (!is_dir(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0755, true);
}

// Helper function to generate absolute URL
function absolute_url($path) {
    return SITE_URL . ltrim($path, '/');
}

// Helper function to log errors
function log_error($message, $level = 'error') {
    $log_file = LOG_DIR . $level . '_' . date('Y-m-d') . '.log';
    $log_message = date('[Y-m-d H:i:s] ') . $message . PHP_EOL;
    error_log($log_message, 3, $log_file);
} 