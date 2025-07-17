<?php
// Prevent direct access to the file
defined('SITE_ROOT') or die('Direct access not allowed');

// Database connection function
function getDbConnection() {
    // Database configuration (consider moving these to a separate config file)
    $host = 'localhost';
    $username = 'root';
    $password = '';
    $database = 'travel_management';
    $port = 3306;

    // Create connection with improved error handling
    $conn = new mysqli($host, $username, $password, $database, $port);

    // Check connection
    if ($conn->connect_error) {
        // Log the error
        error_log("Database Connection Failed: " . $conn->connect_error, 3, 'error_log.txt');
        
        // Depending on environment, you might want to show a generic error or the specific error
        if (defined('DEBUG_MODE') && DEBUG_MODE === true) {
            die("Connection failed: " . $conn->connect_error);
        } else {
            die("Sorry, we're experiencing technical difficulties. Please try again later.");
        }
    }

    // Set character set to utf8mb4 for full Unicode support
    if (!$conn->set_charset("utf8mb4")) {
        error_log("Error setting character set: " . $conn->error, 3, 'error_log.txt');
    }

    return $conn;
}

// Optional: Function to safely close database connection
function closeDbConnection($conn) {
    if ($conn instanceof mysqli) {
        $conn->close();
    }
}

// Optional: Global error handler for database-related errors
function handleDatabaseError($errno, $errstr, $errfile, $errline) {
    // Log the error
    error_log("Database Error [$errno]: $errstr in $errfile on line $errline", 3, 'error_log.txt');
    
    // Depending on environment, you might want to show a generic error or the specific error
    if (defined('DEBUG_MODE') && DEBUG_MODE === true) {
        echo "An error occurred: $errstr";
    } else {
        echo "Sorry, we're experiencing technical difficulties. Please try again later.";
    }
    
    // Don't execute PHP's internal error handler
    return true;
}

// Set the custom error handler
set_error_handler('handleDatabaseError', E_USER_ERROR | E_USER_WARNING); 