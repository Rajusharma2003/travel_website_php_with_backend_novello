<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include necessary files
include_once 'includes/session.php'; // Ensure session is started first
include_once 'includes/config.php';

// Check if admin is logged in using the correct session variable
if (!isset($_SESSION["admin_loggedin"]) || $_SESSION["admin_loggedin"] !== true) {
    header('Location: login.php');
    exit();
}

// Fetch bookings from the database
$sql = "SELECT b.booking_id, u.username, t.tour_name,b.travel_date, b.booking_date, b.status, b.payment_status 
        FROM bookings b 
        JOIN users u ON b.user_id = u.user_id 
        JOIN tours t ON b.tour_id = t.tour_id 
        ORDER BY b.booking_date DESC";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

// Remove mysqli_close($conn); as we are now using PDO
// mysqli_close($conn);

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

    <div class="container-fluid">
        <div class="row">

            <main class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Manage Bookings</h1>
                </div>

                <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="alert alert-success"> <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
                <?php endif; ?>
                <?php if (isset($_SESSION['error_message'])): ?>
                    <div class="alert alert-danger"> <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
                <?php endif; ?>

                <div class="table-responsive">
                    <table class="table table-striped table-sm">
                        <thead>
                            <tr>
                                <th>Booking ID</th>
                                <th>User</th>
                                <th>Tour</th>
                                <th>Travel Date</th>
                                <th>Booking Date</th>
                                <th>Status</th>
                                <th>Payment Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($bookings)): ?>
                                <?php foreach ($bookings as $booking): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($booking['booking_id']); ?></td>
                                        <td><?php echo htmlspecialchars($booking['username']); ?></td>
                                        <td><?php echo htmlspecialchars($booking['tour_name']); ?></td>
                                        <td><?php echo htmlspecialchars($booking['travel_date']); ?></td>
                                        <td><?php echo htmlspecialchars($booking['booking_date']); ?></td>
                                        <td><?php echo htmlspecialchars($booking['status']); ?></td>
                                        <td><?php echo htmlspecialchars($booking['payment_status']); ?></td>
                                        <td>
                                            <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#updateStatusModal" data-bookingid="<?php echo $booking['booking_id']; ?>" data-currentstatus="<?php echo $booking['status']; ?>" data-currentpaymentstatus="<?php echo $booking['payment_status']; ?>">
                                                Update Status
                                            </button>
                                            <a href="view_booking.php?id=<?php echo $booking['booking_id']; ?>" class="btn btn-primary btn-sm">View</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7">No bookings found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Update Status Modal -->
                <div class="modal fade" id="updateStatusModal" tabindex="-1" role="dialog" aria-labelledby="updateStatusModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="updateStatusModalLabel">Update Booking Status</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form action="update_booking_status.php" method="POST">
                                <div class="modal-body">
                                    <input type="hidden" name="booking_id" id="modalBookingId">
                                    <div class="form-group">
                                        <label for="statusSelect">Booking Status</label>
                                        <select class="form-control" id="statusSelect" name="status">
                                            <option value="Pending">Pending</option>
                                            <option value="Confirmed">Confirmed</option>
                                            <option value="Cancelled">Cancelled</option>
                                            <option value="completed">completed</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="paymentStatusSelect">Payment Status</label>
                                        <select class="form-control" id="paymentStatusSelect" name="payment_status">
                                            <option value="Pending">Pending</option>
                                            <option value="paid">paid</option>
                                            <option value="Refunded">Refunded</option>
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

            </main>
        </div>
    </div>
</div>


<script>
    $('#updateStatusModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Button that triggered the modal
        var bookingId = button.data('bookingid'); // Extract info from data-* attributes
        var currentStatus = button.data('currentstatus');
        var currentPaymentStatus = button.data('currentpaymentstatus');

        var modal = $(this);
        modal.find('#modalBookingId').val(bookingId);
        modal.find('#statusSelect').val(currentStatus);
        modal.find('#paymentStatusSelect').val(currentPaymentStatus);
    });
</script>

</body>
</html>