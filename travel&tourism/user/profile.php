<?php
// Set page-specific variables
$pageTitle = "User Profile";
$additionalStyles = ['assets/css/profile.css'];

// Include header
require_once 'includes/header.php';

// Require login
UserAuth::requireLogin();

// Get user details
$user_id = $_SESSION['user_id'];
$db = getDbConnection();

// Fetch user details
$stmt = $db->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Process profile update
$profileUpdateError = '';
$profileUpdateSuccess = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate CSRF token
    if (!UserAuth::validateCSRFToken($_POST['csrf_token'])) {
        $profileUpdateError = "Invalid security token. Please try again.";
    } else {
        // Sanitize inputs
        $username = UserAuth::sanitizeInput($_POST['username']);
        $email = UserAuth::sanitizeInput($_POST['email']);

        // Validate inputs
        if (empty($username) || empty($email)) {
            $profileUpdateError = "Username and email are required.";
        } elseif (strlen($username) < 3) {
            $profileUpdateError = "Username must be at least 3 characters long.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $profileUpdateError = "Invalid email format.";
        } else {
            // Prepare update statement
            $stmt = $db->prepare("UPDATE users SET username = ?, email = ? WHERE user_id = ?");
            $stmt->bind_param("ssi", $username, $email, $user_id);

            if ($stmt->execute()) {
                // Update session variables
                $_SESSION['username'] = $username;
                $_SESSION['email'] = $email;

                $profileUpdateSuccess = "Profile updated successfully!";
                
                // Refresh user data
                $stmt = $db->prepare("SELECT * FROM users WHERE user_id = ?");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $user = $result->fetch_assoc();
            } else {
                $profileUpdateError = "Profile update failed. Please try again.";
            }
            $stmt->close();
        }
    }
}

// Fetch user bookings
$bookings_stmt = $db->prepare("
    SELECT b.*, t.tour_name, t.tour_id 
    FROM bookings b
    JOIN tours t ON b.tour_id = t.tour_id
    WHERE b.user_id = ? 
    ORDER BY b.booking_date DESC 
    LIMIT 5
");
$bookings_stmt->bind_param("i", $user_id);
$bookings_stmt->execute();
$bookings_result = $bookings_stmt->get_result();

// Generate CSRF token
$csrf_token = UserAuth::generateCSRFToken();
?>

<div class="container profile-container">
    <div class="row">
        <div class="col-md-4">
            <div class="profile-sidebar card">
                <div class="profile-avatar text-center">
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($user['username']); ?>&background=007bff&color=fff" 
                         alt="<?php echo htmlspecialchars($user['username']); ?>" 
                         class="rounded-circle mb-3">
                    <h3><?php echo htmlspecialchars($user['username']); ?></h3>
                    <p class="text-muted"><?php echo htmlspecialchars($user['email']); ?></p>
                </div>
                <div class="profile-menu">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="#profile-details" data-bs-toggle="tab">
                                <i class="fas fa-user"></i> Profile Details
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#bookings" data-bs-toggle="tab">
                                <i class="fas fa-suitcase"></i> My Bookings
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#change-password" data-bs-toggle="tab">
                                <i class="fas fa-lock"></i> Change Password
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-danger" href="logout.php">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="profile-content tab-content">
                <!-- Profile Details Tab -->
                <div class="tab-pane active" id="profile-details">
                    <div class="card">
                        <div class="card-header">
                            <h4>Profile Information</h4>
                        </div>
                        <div class="card-body">
                            <?php 
                            if (!empty($profileUpdateError)) {
                                echo "<div class='alert alert-danger'>$profileUpdateError</div>";
                            }
                            if (!empty($profileUpdateSuccess)) {
                                echo "<div class='alert alert-success'>$profileUpdateSuccess</div>";
                            }
                            ?>
                            <form method="POST" action="profile.php">
                                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                
                                <div class="mb-3">
                                    <label for="username" class="form-label">Username</label>
                                    <input type="text" class="form-control" id="username" name="username" 
                                           value="<?php echo htmlspecialchars($user['username']); ?>" 
                                           required minlength="3">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?php echo htmlspecialchars($user['email']); ?>" 
                                           required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Account Created</label>
                                    <input type="text" class="form-control" 
                                           value="<?php echo htmlspecialchars(date('F j, Y', strtotime($user['created_at']))); ?>" 
                                           readonly>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">Update Profile</button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Bookings Tab -->
                <div class="tab-pane" id="bookings">
                    <div class="card">
                        <div class="card-header">
                            <h4>My Bookings</h4>
                        </div>
                        <div class="card-body">
                            <?php if ($bookings_result->num_rows > 0): ?>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Booking ID</th>
                                                <th>Tour</th>
                                                <th>Booking Date</th>
                                                <th>Travel Date</th>
                                                <th>Status</th>
                                                <th>Payment Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($booking = $bookings_result->fetch_assoc()): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($booking['booking_id']); ?></td>
                                                    <td>
                                                        <a href="tours/tour_details.php?id=<?php echo urlencode($booking['tour_id']); ?>">
                                                            <?php echo htmlspecialchars($booking['tour_name']); ?>
                                                        </a>
                                                    </td>
                                                    <td><?php echo htmlspecialchars(date('M j, Y', strtotime($booking['booking_date']))); ?></td>
                                                    <td><?php echo htmlspecialchars(date('M j, Y', strtotime($booking['travel_date']))); ?></td>
                                                    <td>
                                                        <span class="badge 
                                                            <?php 
                                                            switch($booking['status']) {
                                                                case 'confirmed':
                                                                    echo 'bg-success';
                                                                    break;
                                                                case 'pending':
                                                                    echo 'bg-warning';
                                                                    break;
                                                                case 'cancelled':
                                                                    echo 'bg-danger';
                                                                    break;
                                                                default:
                                                                    echo 'bg-secondary';
                                                            }
                                                            ?>
                                                        ">
                                                            <?php echo htmlspecialchars(ucfirst($booking['status'])); ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="badge 
                                                            <?php 
                                                            switch($booking['payment_status']) {
                                                                case 'paid':
                                                                    echo 'bg-success';
                                                                    break;
                                                                case 'pending':
                                                                    echo 'bg-warning';
                                                                    break;
                                                                case 'refunded':
                                                                    echo 'bg-danger';
                                                                    break;
                                                                default:
                                                                    echo 'bg-secondary';
                                                            }
                                                            ?>
                                                        ">
                                                            <?php echo htmlspecialchars(ucfirst($booking['payment_status'])); ?>
                                                        </span>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <p class="text-center">No bookings found.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Change Password Tab -->
                <div class="tab-pane" id="change-password">
                    <div class="card">
                        <div class="card-header">
                            <h4>Change Password</h4>
                        </div>
                        <div class="card-body">
                            <form id="changePasswordForm" method="POST" action="change_password.php">
                                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                
                                <div class="mb-3">
                                    <label for="current_password" class="form-label">Current Password</label>
                                    <input type="password" class="form-control" id="current_password" name="current_password" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="new_password" class="form-label">New Password</label>
                                    <input type="password" class="form-control" id="new_password" name="new_password" 
                                           required minlength="8">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="confirm_new_password" class="form-label">Confirm New Password</label>
                                    <input type="password" class="form-control" id="confirm_new_password" name="confirm_new_password" 
                                           required minlength="8">
                                </div>
                                
                                <button type="submit" class="btn btn-primary">Change Password</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const changePasswordForm = document.getElementById('changePasswordForm');
    
    changePasswordForm.addEventListener('submit', function(event) {
        const newPassword = document.getElementById('new_password').value;
        const confirmNewPassword = document.getElementById('confirm_new_password').value;
        
        if (newPassword !== confirmNewPassword) {
            event.preventDefault();
            alert('New passwords do not match');
        }
    });
});
</script>

<?php
// Include footer
require_once 'includes/footer.php';
?> 