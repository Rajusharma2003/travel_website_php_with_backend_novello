<?php
require_once 'includes/session.php';
include_once 'includes/config.php';


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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Details</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <!-- Your custom style.css should be loaded last to ensure it can override Bootstrap -->
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            .print-area, .print-area * {
                visibility: visible;
            }
            .print-area {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container-fluid">
        <div class="row">
            <main class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom no-print">
                    <h1 class="h2">Booking Details</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <button onclick="window.print()" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-print"></i> Print Booking Details
                        </button>
                    </div>
                </div>

                <div class="print-area">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Booking Information</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>Booking ID:</strong> 
                                            <?php echo htmlspecialchars($booking['booking_id']); ?>
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Booking Date:</strong> 
                                            <?php echo htmlspecialchars(date('d M Y', strtotime($booking['booking_date']))); ?>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-md-6">
                                            <strong>Booking Status:</strong> 
                                            <?php 
                                            $status_class = '';
                                            switch($booking['status']) {
                                                case 'Confirmed':
                                                    $status_class = 'text-success';
                                                    break;
                                                case 'Pending':
                                                    $status_class = 'text-warning';
                                                    break;
                                                case 'Cancelled':
                                                    $status_class = 'text-danger';
                                                    break;
                                            }
                                            echo "<span class='" . $status_class . "'>" . htmlspecialchars($booking['status']); 
                                            ?>
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Payment Status:</strong> 
                                            <?php 
                                            $payment_class = '';
                                            switch($booking['payment_status']) {
                                                case 'Paid':
                                                    $payment_class = 'text-success';
                                                    break;
                                                case 'Pending':
                                                    $payment_class = 'text-warning';
                                                    break;
                                                case 'Failed':
                                                    $payment_class = 'text-danger';
                                                    break;
                                                default:
                                                    $payment_class = 'text-secondary';
                                            }
                                            echo "<span class='" . $payment_class . "'>" . htmlspecialchars($booking['payment_status'] ?? 'Not Specified') . "</span>"; 
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card mt-4">
                                <div class="card-header">
                                    <h3 class="card-title">Customer Details</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>Name:</strong> 
                                            <?php echo htmlspecialchars($user['username'] . ' ' . $user['last_name']); ?>
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Email:</strong> 
                                            <?php echo htmlspecialchars($user['email']); ?>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-md-6">
                                            <strong>Phone:</strong> 
                                            <?php echo htmlspecialchars($user['phone']); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card mt-4">
                                <div class="card-header">
                                    <h3 class="card-title">Tour Details</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>Tour Name:</strong> 
                                            <?php echo htmlspecialchars($tour['tour_name']); ?>
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Tour Price:</strong> 
                                            ₹<?php echo number_format($tour['price'], 2); ?>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-md-6">
                                            <strong>Number of Travelers:</strong> 
                                            <?php echo $booking['number_of_people']; ?>
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Total Amount:</strong> 
                                            ₹<?php echo number_format($tour['price'] * $booking['number_of_people'], 2); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

</body>
</html>