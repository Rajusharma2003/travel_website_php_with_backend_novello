<?php
require_once 'includes/session.php';
require_once 'includes/db_connection.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: bookings.php');
    exit;
}

$booking_id = $_GET['id'];

try {
    $stmt = $pdo->prepare("SELECT * FROM bookings WHERE booking_id = ?");
    $stmt->execute([$booking_id]);
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$booking) {
        header('Location: bookings.php');
        exit;
    }

    // You might also want to fetch related user and tour details
    $user_stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
    $user_stmt->execute([$booking['user_id']]);
    $user = $user_stmt->fetch(PDO::FETCH_ASSOC);

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
    <title>Booking Details</title>
    <!-- Include your CSS files here -->
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container">
        <h1>Booking Details</h1>
        
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Booking #<?php echo $booking['booking_id']; ?></h5>
                
                <div class="row">
                    <div class="col-md-6">
                        <h6>User Information</h6>
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                        <p><strong>Phone:</strong> <?php echo htmlspecialchars($user['phone']); ?></p>
                    </div>
                    
                    <div class="col-md-6">
                        <h6>Tour Information</h6>
                        <p><strong>Tour:</strong> <?php echo htmlspecialchars($tour['title']); ?></p>
                        <p><strong>Price:</strong> $<?php echo number_format($tour['price'], 2); ?></p>
                    </div>
                </div>
                
                <hr>
                
                <div class="row">
                    <div class="col-md-6">
                        <h6>Booking Details</h6>
                        <p><strong>Booking Date:</strong> <?php echo date('F j, Y', strtotime($booking['booking_date'])); ?></p>
                        <p><strong>Travel Date:</strong> <?php echo date('F j, Y', strtotime($booking['travel_date'])); ?></p>
                        <p><strong>Number of People:</strong> <?php echo $booking['number_of_people']; ?></p>
                        <p><strong>Total Price:</strong> $<?php echo number_format($booking['total_price'], 2); ?></p>
                    </div>
                    
                    <div class="col-md-6">
                        <h6>Status</h6>
                        <p><strong>Booking Status:</strong> <?php echo $booking['status']; ?></p>
                        <p><strong>Payment Status:</strong> <?php echo $booking['payment_status']; ?></p>
                        <p><strong>Special Requirements:</strong> <?php echo htmlspecialchars($booking['special_requirement']); ?></p>
                    </div>
                </div>
                
                <div class="mt-3">
                    <a href="bookings.php" class="btn btn-secondary">Back to Bookings</a>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>