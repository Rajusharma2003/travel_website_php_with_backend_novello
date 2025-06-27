<?php
// Include session file
require_once "includes/session.php";

// Check if the user is logged in
check_login();

// Include config file
require_once "includes/config.php";

// Get counts for dashboard
$user_count = 0;
$tour_count = 0;
$booking_count = 0;
$pending_booking_count = 0;

// Count users
$sql = "SELECT COUNT(*) as count FROM users WHERE is_admin = 0";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$user_count = $stmt->fetchColumn();

// Count tours
$sql = "SELECT COUNT(*) as count FROM tours";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$tour_count = $stmt->fetchColumn();

// Count bookings
$sql = "SELECT COUNT(*) as count FROM bookings";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$booking_count = $stmt->fetchColumn();

// Count pending bookings
$sql = "SELECT COUNT(*) as count FROM bookings WHERE status = 'pending'";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$pending_booking_count = $stmt->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Travel Management System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="./assets/css/style.css">
</head>
<body>
      <!-- Import header here -->
      <?php include "includes/header.php"; ?>

    <div class="container-fluid">
        <div class="row">
            <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Dashboard</h1>
                </div>

                <div class="row">
                    <div class="col-md-3 mb-4">
                        <div class="card dashboard-card bg-primary text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title">Users</h6>
                                        <h2 class="mb-0"><?php echo $user_count; ?></h2>
                                    </div>
                                    <i class="fas fa-users fa-2x"></i>
                                </div>
                            </div>
                            <div class="card-footer d-flex align-items-center justify-content-between">
                                <a href="users.php" class="text-white">View Details</a>
                                <i class="fas fa-angle-right"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="card dashboard-card bg-success text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title">Tours</h6>
                                        <h2 class="mb-0"><?php echo $tour_count; ?></h2>
                                    </div>
                                    <i class="fas fa-map-marked-alt fa-2x"></i>
                                </div>
                            </div>
                            <div class="card-footer d-flex align-items-center justify-content-between">
                                <a href="tours.php" class="text-white">View Details</a>
                                <i class="fas fa-angle-right"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="card dashboard-card bg-info text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title">Bookings</h6>
                                        <h2 class="mb-0"><?php echo $booking_count; ?></h2>
                                    </div>
                                    <i class="fas fa-calendar-check fa-2x"></i>
                                </div>
                            </div>
                            <div class="card-footer d-flex align-items-center justify-content-between">
                                <a href="bookings.php" class="text-white">View Details</a>
                                <i class="fas fa-angle-right"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="card dashboard-card bg-warning text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title">Pending Bookings</h6>
                                        <h2 class="mb-0"><?php echo $pending_booking_count; ?></h2>
                                    </div>
                                    <i class="fas fa-clock fa-2x"></i>
                                </div>
                            </div>
                            <div class="card-footer d-flex align-items-center justify-content-between">
                                <a href="bookings.php?status=pending" class="text-white">View Details</a>
                                <i class="fas fa-angle-right"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Recent Bookings</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-sm">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>User</th>
                                                <th>Tour</th>
                                                <th>Travel Date</th>
                                                <th>Booking Date</th>
                                                <th>People</th>
                                                <th>Total</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            // Get recent bookings
                                            $sql = "SELECT b.booking_id, u.username, t.tour_name, b.travel_date,b.booking_date, b.number_of_people, b.total_price, b.status 
                                                    FROM bookings b 
                                                    JOIN users u ON b.user_id = u.user_id 
                                                    JOIN tours t ON b.tour_id = t.tour_id 
                                                    ORDER BY b.booking_date DESC LIMIT 5";
                                            $stmt = $pdo->prepare($sql);
                                            $stmt->execute();
                                            
                                            if($stmt->rowCount() > 0){
                                                while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                                                    echo "<tr>";
                                                    echo "<td>" . $row['booking_id'] . "</td>";
                                                    echo "<td>" . $row['username'] . "</td>";
                                                    echo "<td>" . $row['tour_name'] . "</td>";
                                                    echo "<td>" . $row['travel_date'] . "</td>";
                                                    echo "<td>" . $row['booking_date'] . "</td>";
                                                    echo "<td>" . $row['number_of_people'] . "</td>";
                                                    echo "<td>₹" . $row['total_price'] . "</td>";
                                                    echo "<td>";
                                                    if($row['status'] == 'pending'){
                                                        echo "<span class='badge badge-warning'>Pending</span>";
                                                    } else if($row['status'] == 'confirmed'){
                                                        echo "<span class='badge badge-success'>Confirmed</span>";
                                                    } else if($row['status'] == 'cancelled'){
                                                        echo "<span class='badge badge-danger'>Cancelled</span>";
                                                    } else {
                                                        echo "<span class='badge badge-info'>Completed</span>";
                                                    }
                                                    echo "</td>";
                                                    echo "<td>
                                                            <a href='view_booking.php?id=" . $row['booking_id'] . "' class='btn btn-sm btn-info'><i class='fas fa-eye'></i></a>
                                                            <a href='edit_booking.php?id=" . $row['booking_id'] . "' class='btn btn-sm btn-primary'><i class='fas fa-edit'></i></a>
                                                          </td>";
                                                    echo "</tr>";
                                                }
                                            } else {
                                                echo "<tr><td colspan='9'>No recent bookings found.</td></tr>";
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>


    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="./assets/js/script.js"></script>
</body>
</html>