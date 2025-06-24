<?php
require_once 'includes/session.php';
require_once 'includes/db_connection.php';

// Ensure the user is logged in as admin
check_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_id = filter_input(INPUT_POST, 'booking_id', FILTER_SANITIZE_NUMBER_INT);
    $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);
    $payment_status = filter_input(INPUT_POST, 'payment_status', FILTER_SANITIZE_STRING);

    // Validate inputs
    if (!$booking_id || !in_array($status, ['Pending', 'Confirmed', 'Cancelled', 'Completed']) || !in_array($payment_status, ['Pending', 'Paid', 'Refunded'])) {
        $_SESSION['error_message'] = 'Invalid input for booking status update.';
        header('Location: bookings.php');
        exit;
    }

    try {
        $stmt = $pdo->prepare("UPDATE bookings SET status = ?, payment_status = ?, updated_at = CURRENT_TIMESTAMP WHERE booking_id = ?");
        $stmt->execute([$status, $payment_status, $booking_id]);

        if ($stmt->rowCount() > 0) {
            $_SESSION['success_message'] = 'Booking status updated successfully!';
        } else {
            $_SESSION['error_message'] = 'No changes made or booking not found.';
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