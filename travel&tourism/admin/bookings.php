<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start(); // Ensure session is started

// Include necessary files
include_once 'includes/config.php';
include_once 'includes/session.php';

// Check if admin is logged in using the correct session variable
if (!isset($_SESSION["admin_loggedin"]) || $_SESSION["admin_loggedin"] !== true) {
    header('Location: login.php');
    exit();
}

// Fetch bookings from the database
// This is a placeholder. You'll need to implement actual database queries here.
$bookings = []; // Replace with actual data from your database

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Bookings - Admin Panel</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <!-- Your custom style.css should be loaded last to ensure it can override Bootstrap -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container">
        <h1 style="display: flex; justify-content: center; align-items: center;">Manage Bookings</h1>

        <?php if (empty($bookings)): ?>
            <p style="display: flex; justify-content: center; align-items: center;">No bookings found.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>User</th>
                        <th>Tour</th>
                        <th>Booking Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $booking): // Loop through bookings ?>
                        <tr>
                            <td><?php echo htmlspecialchars($booking['booking_id']); ?></td>
                            <td><?php echo htmlspecialchars($booking['user_name']); ?></td>
                            <td><?php echo htmlspecialchars($booking['tour_name']); ?></td>
                            <td><?php echo htmlspecialchars($booking['booking_date']); ?></td>
                            <td><?php echo htmlspecialchars($booking['status']); ?></td>
                            <td>
                                <a href="#" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#updateStatusModal<?php echo $booking['booking_id']; ?>">Update Status</a>
                                <a href="view_booking.php?id=<?php echo $booking['booking_id']; ?>" class="btn btn-info btn-sm">View</a>
                            </td>
                        </tr>
                        <!-- Update Status Modal for Booking ID: <?php echo $booking['booking_id']; ?> -->
                        <div class="modal fade" id="updateStatusModal<?php echo $booking['booking_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="updateStatusModalLabel<?php echo $booking['booking_id']; ?>" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="updateStatusModalLabel<?php echo $booking['booking_id']; ?>">Update Status for Booking #<?php echo $booking['booking_id']; ?></h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <form action="update_booking_status.php" method="POST">
                                        <div class="modal-body">
                                            <input type="hidden" name="booking_id" value="<?php echo $booking['booking_id']; ?>">
                                            <div class="form-group">
                                                <label for="status">Booking Status</label>
                                                <select class="form-control" id="status" name="status">
                                                    <option value="Pending" <?php echo ($booking['status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                                                    <option value="Confirmed" <?php echo ($booking['status'] == 'Confirmed') ? 'selected' : ''; ?>>Confirmed</option>
                                                    <option value="Cancelled" <?php echo ($booking['status'] == 'Cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                                                    <option value="Completed" <?php echo ($booking['status'] == 'Completed') ? 'selected' : ''; ?>>Completed</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="payment_status">Payment Status</label>
                                                <select class="form-control" id="payment_status" name="payment_status">
                                                    <option value="Pending" <?php echo ($booking['payment_status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                                                    <option value="Paid" <?php echo ($booking['payment_status'] == 'Paid') ? 'selected' : ''; ?>>Paid</option>
                                                    <option value="Refunded" <?php echo ($booking['payment_status'] == 'Refunded') ? 'selected' : ''; ?>>Refunded</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary">Save changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

</body>
</html>