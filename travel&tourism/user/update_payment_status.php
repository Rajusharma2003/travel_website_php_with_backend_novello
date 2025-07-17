<?php
// Set page-specific variables
$pageTitle = "Update Payment Status";

// Include necessary files
require_once 'includes/header.php';
require_once 'includes/functions.php';

// Require login
UserAuth::requireLogin();

// Check if form is submitted via POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: profile.php');
    exit();
}

// Validate CSRF token (optional, but recommended)
if (!UserAuth::validateCSRFToken($_POST['csrf_token'] ?? '')) {
    $_SESSION['error_message'] = "Invalid security token. Please try again.";
    header('Location: profile.php');
    exit();
}

// Validate booking ID
$booking_id = filter_input(INPUT_POST, 'booking_id', FILTER_VALIDATE_INT);
if (!$booking_id) {
    $_SESSION['error_message'] = "Invalid booking ID.";
    header('Location: profile.php');
    exit();
}

// Validate payment status
$payment_status_options = ['pending', 'paid', 'refunded'];
$payment_status = filter_input(INPUT_POST, 'payment_status', FILTER_VALIDATE_REGEXP, 
    ['options' => ['regexp' => '/^(' . implode('|', $payment_status_options) . ')$/']]);
if (!$payment_status) {
    $_SESSION['error_message'] = "Invalid payment status.";
    header('Location: profile.php');
    exit();
}

// Get database connection
$db = getDbConnection();

// Prepare update statement
$stmt = $db->prepare("UPDATE bookings SET payment_status = ? WHERE booking_id = ? AND user_id = ?");
$stmt->bind_param("sii", $payment_status, $booking_id, $_SESSION['user_id']);

try {
    $stmt->execute();
    
    // Check if any rows were affected
    if ($stmt->affected_rows > 0) {
        $_SESSION['success_message'] = "Payment status updated successfully.";
    } else {
        $_SESSION['error_message'] = "Unable to update payment status. Booking not found.";
    }
    
    header('Location: profile.php?section=bookings');
    exit();
    
} catch (Exception $e) {
    // Log the error
    error_log("Payment Status Update Error: " . $e->getMessage());
    
    // Set error message
    $_SESSION['error_message'] = "An error occurred while updating payment status. Please try again.";
    header('Location: profile.php?section=bookings');
    exit();
}

// Include footer
require_once 'includes/footer.php';
?> 