<?php
require_once 'includes/session.php';
include_once 'includes/config.php';

// Check if user is logged in and has admin privileges
if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

// Check if booking ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: bookings.php');
    exit;
}

$booking_id = $_GET['id'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate and sanitize input
        $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);
        $payment_status = filter_input(INPUT_POST, 'payment_status', FILTER_SANITIZE_STRING);
        $number_of_people = filter_input(INPUT_POST, 'number_of_people', FILTER_VALIDATE_INT);

        // Prepare update statement
        $stmt = $pdo->prepare("UPDATE bookings SET status = ?, payment_status = ?, number_of_people = ? WHERE booking_id = ?");
        $stmt->execute([$status, $payment_status, $number_of_people, $booking_id]);

        // Redirect with success message
        $_SESSION['message'] = "Booking updated successfully!";
        header('Location: view_booking.php?id=' . $booking_id);
        exit;
    } catch (PDOException $e) {
        $error = "Database error: " . $e->getMessage();
    }
}

// Fetch current booking details
try {
    $stmt = $pdo->prepare("SELECT * FROM bookings WHERE booking_id = ?");
    $stmt->execute([$booking_id]);
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$booking) {
        header('Location: bookings.php');
        exit;
    }

    // Fetch related tour details for price calculation
    $tour_stmt = $pdo->prepare("SELECT * FROM tours WHERE tour_id = ?");
    $tour_stmt->execute([$booking['tour_id']]);
    $tour = $tour_stmt->fetch(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Booking</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container-fluid">
        <div class="row">
            <main class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Edit Booking</h1>
                </div>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Booking Information</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label for="booking_id">Booking ID</label>
                                    <input type="text" class="form-control" id="booking_id" value="<?php echo htmlspecialchars($booking['booking_id']); ?>" readonly>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for="booking_date">Booking Date</label>
                                    <input type="text" class="form-control" id="booking_date" value="<?php echo htmlspecialchars(date('d M Y', strtotime($booking['booking_date']))); ?>" readonly>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label for="status">Booking Status</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="Confirmed" <?php echo $booking['status'] === 'Confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                        <option value="Pending" <?php echo $booking['status'] === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="Cancelled" <?php echo $booking['status'] === 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                    </select>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for="payment_status">Payment Status</label>
                                    <select name="payment_status" id="payment_status" class="form-control">
                                        <option value="Paid" <?php echo $booking['payment_status'] === 'Paid' ? 'selected' : ''; ?>>Paid</option>
                                        <option value="Pending" <?php echo $booking['payment_status'] === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="Failed" <?php echo $booking['payment_status'] === 'Failed' ? 'selected' : ''; ?>>Failed</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label for="number_of_people">Number of Travelers</label>
                                    <input type="number" name="number_of_people" id="number_of_people" class="form-control" 
                                           value="<?php echo htmlspecialchars($booking['number_of_people']); ?>" 
                                           min="1" max="10" required>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for="total_amount">Total Amount</label>
                                    <input type="text" id="total_amount" class="form-control" 
                                           value="₹<?php echo number_format($tour['price'] * $booking['number_of_people'], 2); ?>" 
                                           readonly>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">Update Booking</button>
                            <a href="view_booking.php?id=<?php echo $booking_id; ?>" class="btn btn-secondary ml-2">Cancel</a>
                        </div>
                    </div>
                </form>
            </main>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        // Update total amount when number of travelers changes
        document.getElementById('number_of_people').addEventListener('change', function() {
            const tourPrice = <?php echo $tour['price']; ?>;
            const totalAmount = tourPrice * this.value;
            document.getElementById('total_amount').value = '₹' + totalAmount.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        });
    </script>
</body>
</html> 