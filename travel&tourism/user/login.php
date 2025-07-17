<?php
// Set page-specific variables
$pageTitle = "Login";
$additionalStyles = ['assets/css/auth.css'];

// Include header
require_once 'includes/header.php';

// Check if user is already logged in
if (UserAuth::isLoggedIn()) {
    header("Location: index.php");
    exit();
}

// Process login form submission
$loginError = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Validate CSRF token
        if (!isset($_POST['csrf_token']) || !UserAuth::validateCSRFToken($_POST['csrf_token'])) {
            throw new Exception("Invalid security token. Please try again.");
        }

        // Validate input
        if (empty($_POST['email']) || empty($_POST['password'])) {
            throw new Exception("Email and password are required.");
        }

        // Sanitize inputs
        $email = UserAuth::sanitizeInput($_POST['email']);
        $password = $_POST['password'];

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format.");
        }

        // Database connection
        $db = getDbConnection();

        // Prepare SQL to prevent SQL injection
        $stmt = $db->prepare("SELECT user_id, username, phone, email, is_admin, profile_image, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            throw new Exception("No account found with this email address.");
        }

        $user = $result->fetch_assoc();

        // Check user account status (you might want to add a status column to your users table)
        // For now, we'll use is_admin as a proxy for account status
        if ($user['is_admin'] === null) {
            throw new Exception("Your account is not active. Please contact support.");
        }

        // Verify password
        // Debug: print out details about password verification
        error_log("Password verification debug:");
        error_log("Input password length: " . strlen($password));
        error_log("Stored hash: " . $user['password']);
        
        // Attempt to verify password with different methods
        $passwordVerified = false;
        
        // Method 1: Standard password_verify
        if (password_verify($password, $user['password'])) {
            $passwordVerified = true;
            error_log("Password verified successfully with password_verify()");
        } else {
            error_log("Standard password_verify() failed");
            
            // Method 2: Check for potential encoding issues
            $utf8Password = mb_convert_encoding($password, 'UTF-8', 'auto');
            if (password_verify($utf8Password, $user['password'])) {
                $passwordVerified = true;
                error_log("Password verified with UTF-8 encoding");
            } else {
                error_log("UTF-8 encoding verification failed");
            }
        }

        // Throw exception if password cannot be verified
        if (!$passwordVerified) {
            // Log detailed failed login attempt
            error_log("Failed login attempt details:");
            error_log("Email: $email");
            error_log("Stored hash: " . $user['password']);
            error_log("Attempted password length: " . strlen($password));
            error_log("IP: " . $_SERVER['REMOTE_ADDR']);
            
            throw new Exception("Invalid email or password.");
        }

        // Start session and store user data
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $email;
        $_SESSION['role'] = $user['is_admin'] ? 'admin' : 'user';
        $_SESSION['profile_image'] = $user['profile_image'] ?? '';

        // Log successful login
        error_log("Successful login for user: {$user['username']} (ID: {$user['user_id']}) from IP: " . $_SERVER['REMOTE_ADDR']);

        // Redirect based on user role
        if ($user['is_admin']) {
            header("Location: ../admin/index.php");
        } else {
            header("Location: index.php");
        }
        exit();

    } catch (Exception $e) {
        // Capture and display error
        $loginError = $e->getMessage();
    } finally {
        // Close database connection if open
        if (isset($stmt)) $stmt->close();
        if (isset($db)) $db->close();
    }
}

// Generate CSRF token
$csrf_token = UserAuth::generateCSRFToken();
?>

<div class="container auth-container pt-5 mt-5">
    <div class="row justify-content-center">
        <div class="col-md-12 mx-auto">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white text-center">
                    <h2>Login to Trae Travel</h2>
                </div>
                <div class="card-body">
                    <?php 
                    // Display login error if exists
                    if (!empty($loginError)) {
                        echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                                $loginError
                                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                              </div>";
                    }
                    ?>
                    <form method="POST" action="login.php" id="loginForm">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" class="form-control" id="email" name="email" 
                                       placeholder="Enter your email" 
                                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" 
                                       required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" id="password" name="password" 
                                       placeholder="Enter your password" required>
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="form-text">
                                <a href="forgot_password.php" class="text-muted">Forgot Password?</a>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Login</button>
                        </div>
                    </form>
                    
                    <div class="text-center mt-3">
                        <p>Don't have an account? <a href="register.php">Register here</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('password');
    const togglePasswordBtn = document.getElementById('togglePassword');
    const loginForm = document.getElementById('loginForm');

    // Password visibility toggle
    togglePasswordBtn.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        
        // Toggle eye icon
        this.querySelector('i').classList.toggle('fa-eye');
        this.querySelector('i').classList.toggle('fa-eye-slash');
    });

    // Form validation
    loginForm.addEventListener('submit', function(event) {
        const email = document.getElementById('email').value.trim();
        const password = passwordInput.value.trim();

        // Basic client-side validation
        if (!email || !password) {
            event.preventDefault();
            alert('Please enter both email and password');
        }
    });
});
</script>

<?php
// Include footer
require_once 'includes/footer.php';
?> 