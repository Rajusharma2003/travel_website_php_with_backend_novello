<?php
require_once 'includes/session.php';
include_once 'includes/config.php';
// Ensure the user is logged in as admin
check_login();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Debugging: Log all POST data
    error_log("POST Data: " . print_r($_POST, true));

    $booking_id = $_POST['booking_id'] ?? '';
    $status = $_POST['status'] ?? '';
    $valid_payment_statuses = ['Pending', 'paid', 'Refunded'];

    // If payment_status is 'Completed' from the form, change it to 'paid' for database storage
    if (isset($_POST['payment_status']) && $_POST['payment_status'] === 'Completed') {
        $_POST['payment_status'] = 'paid';
    }

    $payment_status = $_POST['payment_status'] ?? '';

    // If payment_status is empty, default it to 'Pending'
    if (empty($payment_status)) {
        $payment_status = 'Pending';
    }

    // Validate inputs separately
    $valid_statuses = ['Pending', 'Confirmed', 'Cancelled', 'completed'];
    $valid_payment_statuses = ['Pending', 'paid', 'Refunded' ];

    $status_error = !empty($status) && !in_array($status, $valid_statuses);
    $payment_error = !empty($payment_status) && !in_array($payment_status, $valid_payment_statuses);

    if ($status_error || $payment_error) {
        $_SESSION['error_message'] = "Invalid input for booking status update.";
        if ($status_error) {
            $_SESSION['error_message'] .= " Received invalid Status: '" . htmlspecialchars($status) . "'.";
        }
        if ($payment_error) {
            $_SESSION['error_message'] .= " Received invalid Payment Status: '" . htmlspecialchars($payment_status) . "'.";
        }
        header('Location: bookings.php');
        exit();
    }

    try {
        // Only update fields that have values
        // Build SQL query dynamically based on which fields have values
        $sql = "UPDATE bookings SET ";
        $params = [];
        
        if (!empty($status)) {
            $sql .= "status = :status, ";
            $params[':status'] = $status;
        }
        if (!empty($payment_status)) {
            $sql .= "payment_status = :payment_status, ";
            $params[':payment_status'] = $payment_status;
        }
        
        // Remove trailing comma if we added fields
        if (!empty($params)) {
            $sql = rtrim($sql, ", ");
        }
        
        $sql .= " WHERE booking_id = :booking_id";
        $params[':booking_id'] = $booking_id;

        $stmt = $pdo->prepare($sql);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        if ($stmt->execute()) {
            if ($stmt->rowCount() > 0) {
                $_SESSION['success_message'] = 'Booking status updated successfully!';
            } else {
                $_SESSION['error_message'] = 'No changes made or booking not found.';
            }
        } else {
            $_SESSION['error_message'] = 'Database error: ' . implode(", ", $stmt->errorInfo());
        }

    } catch (PDOException $e) {
        $_SESSION['error_message'] = 'Database error: ' . $e->getMessage();
    }

    header('Location: bookings.php');
    exit;
} else {
    // Redirect if accessed directly without POST request
    header('Location: bookings.php');
    exit;
}
?>