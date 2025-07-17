<?php
session_start();
include 'includes/header.php';
require_once 'includes/db_connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

$conn = getDbConnection();

// Fetch bookings for the logged-in user
$sql = "SELECT b.booking_id, t.tour_name, b.travel_date, b.booking_date, b.status, b.payment_status, b.total_price 
        FROM bookings b 
        JOIN tours t ON b.tour_id = t.tour_id 
        WHERE b.user_id = ? 
        ORDER BY b.booking_date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$bookings = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $bookings[] = $row;
    }
}

$stmt->close();
closeDbConnection($conn);
?>

<div class="container my-5 pt-4">
    <h2 class="text-center mb-4">My Bookings</h2>
    <div class="row">
        <div class="col-md-10 mx-auto">
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="table-responsive">
                <table class="table table-striped table-hover table-sm">
                    <thead>
                        <tr>
                            <th>Booking ID</th>
                            <th>Tour Name</th>
                            <th>Travel Date</th>
                            <th>Booking Date</th>
                            <th>Status</th>
                            <th>Payment Status</th>
                            <th>Total Price</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($bookings)): ?>
                            <?php foreach ($bookings as $booking): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($booking['booking_id']); ?></td>
                                    <td><?php echo htmlspecialchars($booking['tour_name']); ?></td>
                                    <td><?php echo htmlspecialchars($booking['travel_date']); ?></td>
                                    <td><?php echo htmlspecialchars($booking['booking_date']); ?></td>
                                    <td><?php echo htmlspecialchars($booking['status']); ?></td>
                                    <td><?php echo htmlspecialchars($booking['payment_status']); ?></td>
                                    <td>₹<?php echo number_format($booking['total_price'], 2); ?></td>
                                    <td>
                                        <a href="tour_details.php?id=<?php echo htmlspecialchars($booking['tour_id']); ?>" class="btn btn-info btn-sm">View Tour</a>
                                        <!-- Add more user-specific actions here, e.g., Cancel, if applicable -->
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center">No bookings found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
include 'includes/footer.php';
?> 