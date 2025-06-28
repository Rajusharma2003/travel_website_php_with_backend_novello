<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection parameters
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'travel_management');

// Function to log database errors
function logDatabaseError($message) {
    error_log("Database Error: " . $message);
    // Optionally, you can also write to a file
    file_put_contents(__DIR__ . '/db_error_log.txt', date('[Y-m-d H:i:s] ') . $message . PHP_EOL, FILE_APPEND);
}

// Attempt to connect to MySQL database using PDO
$pdo = null;
try {
    // Detailed PDO connection
    $pdo = new PDO(
        "mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME . ";charset=utf8mb4", 
        DB_USERNAME, 
        DB_PASSWORD,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_TIMEOUT => 5
        ]
    );

    // Verify database connection
    $testQuery = $pdo->query("SELECT 1");
    if (!$testQuery) {
        throw new PDOException("Unable to verify database connection");
    }
} catch(PDOException $e) {
    // Log the full error details
    logDatabaseError("PDO Connection Error: " . $e->getMessage());
    
    // Set a session error for user-friendly display
    session_start();
    $_SESSION['db_connection_error'] = "Database connection failed. Please contact support.";
    
    // Optionally, you can redirect to an error page
    // header('Location: error.php');
    // exit();
}

// Mysqli connection (for legacy code)
$conn = null;
try {
    $conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
    
    if (!$conn) {
        throw new Exception("Mysqli connection failed: " . mysqli_connect_error());
    }
} catch(Exception $e) {
    logDatabaseError("Mysqli Connection Error: " . $e->getMessage());
}
?>