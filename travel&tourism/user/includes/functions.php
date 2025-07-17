<?php
// User-specific utility functions

// Prevent direct access to the file
if (!defined('SITE_ROOT')) {
    die('Direct access not allowed');
}

// Include necessary files
require_once 'config.php';
require_once 'db_connection.php';

// User Authentication Functions
class UserAuth {
    // Generate CSRF Token
    public static function generateCSRFToken() {
        if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
            $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
        }
        return $_SESSION[CSRF_TOKEN_NAME];
    }

    // Validate CSRF Token
    public static function validateCSRFToken($token) {
        return isset($_SESSION[CSRF_TOKEN_NAME]) && 
               hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
    }

    // Check if user is logged in
    public static function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    // Redirect if not logged in
    public static function requireLogin() {
        if (!self::isLoggedIn()) {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
            header("Location: ../account/login.php");
            exit();
        }
    }

    // Sanitize user input
    public static function sanitizeInput($input) {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
}

// File Upload Utility Functions
class FileUpload {
    // Validate and upload file
    public static function uploadFile($file, $uploadDir = null, $allowedTypes = null) {
        // Use default values from config if not provided
        $uploadDir = $uploadDir ?? UPLOAD_DIR;
        $allowedTypes = $allowedTypes ?? ALLOWED_FILE_TYPES;
        $maxFileSize = MAX_FILE_SIZE;

        // Check if file was uploaded successfully
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'File upload failed.'];
        }

        // Get file details
        $fileName = $file['name'];
        $fileTmpName = $file['tmp_name'];
        $fileSize = $file['size'];
        $fileError = $file['error'];

        // Get file extension
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        // Validate file type
        if (!in_array($fileExt, $allowedTypes)) {
            return ['success' => false, 'message' => 'Invalid file type.'];
        }

        // Validate file size
        if ($fileSize > $maxFileSize) {
            return ['success' => false, 'message' => 'File is too large.'];
        }

        // Generate unique filename
        $newFileName = uniqid('upload_', true) . '.' . $fileExt;
        $uploadPath = $uploadDir . $newFileName;

        // Move uploaded file
        if (move_uploaded_file($fileTmpName, $uploadPath)) {
            return [
                'success' => true, 
                'message' => 'File uploaded successfully.',
                'filename' => $newFileName
            ];
        }

        return ['success' => false, 'message' => 'File upload failed.'];
    }
}

// Utility Functions
class Utilities {
    // Format date
    public static function formatDate($date, $format = 'M d, Y') {
        return date($format, strtotime($date));
    }

    // Truncate text
    public static function truncateText($text, $length = 100, $ellipsis = '...') {
        if (strlen($text) <= $length) {
            return $text;
        }
        return rtrim(substr($text, 0, $length)) . $ellipsis;
    }

    // Generate random string
    public static function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}

// Error Handling
function logError($message, $file = null, $line = null) {
    $logMessage = date('[Y-m-d H:i:s] ') . $message;
    if ($file) $logMessage .= " in file: $file";
    if ($line) $logMessage .= " on line: $line";
    
    // Log to file
    error_log($logMessage, 3, UPLOAD_DIR . 'error_log.txt');
}

// Global error handler
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    // Log all errors
    logError($errstr, $errfile, $errline);
    
    // Don't execute PHP's internal error handler
    return true;
});

// Sanitize user input
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Validate email address
function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Generate a secure password hash
function generate_password_hash($password) {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
}

// Verify password
function verify_password($input_password, $stored_hash) {
    return password_verify($input_password, $stored_hash);
}

// Check if user is logged in
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

// Redirect user
function redirect($location) {
    header("Location: $location");
    exit();
}

// Display error messages
function display_error($message) {
    return "<div class='alert alert-danger'>$message</div>";
}

// Display success messages
function display_success($message) {
    return "<div class='alert alert-success'>$message</div>";
}

// Generate a unique token for CSRF protection
function generate_csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Validate CSRF token
function validate_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Log user activity
function log_user_activity($user_id, $activity) {
    global $conn; // Assuming database connection is available
    $activity = sanitize_input($activity);
    $stmt = $conn->prepare("INSERT INTO user_activity_log (user_id, activity, timestamp) VALUES (?, ?, NOW())");
    $stmt->bind_param("is", $user_id, $activity);
    $stmt->execute();
    $stmt->close();
}

// Get user details by ID
function get_user_details($user_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    return $user;
}

// Update user profile
function update_user_profile($user_id, $data) {
    global $conn;
    $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, phone = ? WHERE id = ?");
    $stmt->bind_param("sssi", $data['name'], $data['email'], $data['phone'], $user_id);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}

// Pagination helper function
function paginate($total_items, $items_per_page, $current_page) {
    $total_pages = ceil($total_items / $items_per_page);
    $start = ($current_page - 1) * $items_per_page;
    
    return [
        'total_pages' => $total_pages,
        'start' => $start,
        'current_page' => $current_page
    ];
}

// Format price
function format_price($price) {
    return '₹ ' . number_format($price, 2);
}

// Get client IP address
function get_client_ip() {
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
        $ipaddress = getenv('HTTP_FORWARDED');
    else if(getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}
?> 