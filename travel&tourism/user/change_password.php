<?php
// Set page-specific variables
$pageTitle = "Change Password";

// Include header
require_once 'includes/header.php';

// Require login
UserAuth::requireLogin();

$changePasswordError = '';
$changePasswordSuccess = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate CSRF token
    if (!UserAuth::validateCSRFToken($_POST['csrf_token'])) {
        $changePasswordError = "Invalid security token. Please try again.";
    } else {
        // Get current user ID
        $user_id = $_SESSION['user_id'];

        // Sanitize inputs
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_new_password = $_POST['confirm_new_password'];

        // Validate inputs
        if (empty($current_password) || empty($new_password) || empty($confirm_new_password)) {
            $changePasswordError = "All fields are required.";
        } elseif (strlen($new_password) < 8) {
            $changePasswordError = "New password must be at least 8 characters long.";
        } elseif ($new_password !== $confirm_new_password) {
            $changePasswordError = "New passwords do not match.";
        } else {
            // Database connection
            $db = getDbConnection();

            // Fetch current password hash
            $stmt = $db->prepare("SELECT password FROM users WHERE user_id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();

            // Verify current password
            if (!password_verify($current_password, $user['password'])) {
                $changePasswordError = "Current password is incorrect.";
            } else {
                // Hash new password
                $new_password_hash = password_hash($new_password, PASSWORD_BCRYPT);

                // Update password
                $update_stmt = $db->prepare("UPDATE users SET password = ? WHERE user_id = ?");
                $update_stmt->bind_param("si", $new_password_hash, $user_id);

                if ($update_stmt->execute()) {
                    $changePasswordSuccess = "Password changed successfully!";
                    
                    // Optional: Send password change notification email
                    $email = $_SESSION['email'];
                    $to = $email;
                    $subject = "Password Changed - Trae Travel";
                    $message = "Your account password was recently changed. If this was not you, please contact support immediately.";
                    $headers = "From: noreply@traetravel.com";
                    @mail($to, $subject, $message, $headers);
                } else {
                    $changePasswordError = "Password update failed. Please try again.";
                }
                $update_stmt->close();
            }
            $stmt->close();
            $db->close();
        }
    }
}

// Generate CSRF token
$csrf_token = UserAuth::generateCSRFToken();
?>

<div class="container auth-container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white text-center">
                    <h2>Change Password</h2>
                </div>
                <div class="card-body">
                    <?php 
                    // Display change password error or success message
                    if (!empty($changePasswordError)) {
                        echo "<div class='alert alert-danger'>$changePasswordError</div>";
                    }
                    if (!empty($changePasswordSuccess)) {
                        echo "<div class='alert alert-success'>$changePasswordSuccess</div>";
                    }
                    ?>
                    <form method="POST" action="change_password.php" id="changePasswordForm">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                        
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Current Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" id="current_password" name="current_password" 
                                       placeholder="Enter current password" required>
                                <button class="btn btn-outline-secondary" type="button" id="toggleCurrentPassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="new_password" class="form-label">New Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" id="new_password" name="new_password" 
                                       placeholder="Enter new password" 
                                       required minlength="8">
                                <button class="btn btn-outline-secondary" type="button" id="toggleNewPassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="form-text text-muted">
                                Password must be at least 8 characters long
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="confirm_new_password" class="form-label">Confirm New Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" id="confirm_new_password" name="confirm_new_password" 
                                       placeholder="Confirm new password" 
                                       required minlength="8">
                                <button class="btn btn-outline-secondary" type="button" id="toggleConfirmNewPassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Change Password</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const passwordFields = [
        { input: document.getElementById('current_password'), toggleBtn: document.getElementById('toggleCurrentPassword') },
        { input: document.getElementById('new_password'), toggleBtn: document.getElementById('toggleNewPassword') },
        { input: document.getElementById('confirm_new_password'), toggleBtn: document.getElementById('toggleConfirmNewPassword') }
    ];

    // Password toggle functionality
    passwordFields.forEach(({ input, toggleBtn }) => {
        toggleBtn.addEventListener('click', function() {
            const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
            input.setAttribute('type', type);
            
            // Toggle eye icon
            this.querySelector('i').classList.toggle('fa-eye');
            this.querySelector('i').classList.toggle('fa-eye-slash');
        });
    });

    // Form validation
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