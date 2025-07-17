<?php
// Set page-specific variables
$pageTitle = "Register";
$additionalStyles = ['assets/css/auth.css'];

// Include header
require_once 'includes/header.php';

// Check if user is already logged in
if (UserAuth::isLoggedIn()) {
    header("Location: profile.php");
    exit();
}

// Process registration form submission
$registrationError = '';
$registrationSuccess = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate CSRF token
    if (!UserAuth::validateCSRFToken($_POST['csrf_token'])) {
        $registrationError = "Invalid security token. Please try again.";
    } else {
        // Sanitize and validate inputs
        $username = UserAuth::sanitizeInput($_POST['username']);
        $email = UserAuth::sanitizeInput($_POST['email']);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        // Validate inputs
        if (empty($username) || empty($email) || empty($password)) {
            $registrationError = "All fields are required.";
        } elseif (strlen($username) < 3) {
            $registrationError = "Username must be at least 3 characters long.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $registrationError = "Invalid email format.";
        } elseif (strlen($password) < 8) {
            $registrationError = "Password must be at least 8 characters long.";
        } elseif ($password !== $confirm_password) {
            $registrationError = "Passwords do not match.";
        } else {
            // Database connection
            $db = getDbConnection();

            // Check if email already exists
            $stmt = $db->prepare("SELECT user_id FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $registrationError = "Email already registered.";
                $stmt->close();
            } else {
                // Hash password
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);

                // Prepare insert statement
                $stmt = $db->prepare("INSERT INTO users (username, email, password, is_admin, created_at) VALUES (?, ?, ?, 0, NOW())");
                $stmt->bind_param("sss", $username, $email, $hashed_password);

                if ($stmt->execute()) {
                    $registrationSuccess = "Registration successful! Please log in.";
                    
                    // Optional: Send welcome email
                    $to = $email;
                    $subject = "Welcome to Trae Travel";
                    $message = "Hello $username,\n\nThank you for registering with Trae Travel. We're excited to have you on board!\n\nBest regards,\nTrae Travel Team";
                    $headers = "From: noreply@traetravel.com";
                    @mail($to, $subject, $message, $headers);
                } else {
                    $registrationError = "Registration failed. Please try again.";
                }
                $stmt->close();
            }
            $db->close();
        }
    }
}

// Generate CSRF token
$csrf_token = UserAuth::generateCSRFToken();
?>

<div class="container auth-container pt-5 mt-5">
    <div class="row justify-content-center m-5">
        <div class="col-md-12">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white text-center">
                    <h2>Create Your Account</h2>
                </div>
                <div class="card-body">
                    <?php 
                    // Display registration error or success message
                    if (!empty($registrationError)) {
                        echo "<div class='alert alert-danger'>$registrationError</div>";
                    }
                    if (!empty($registrationSuccess)) {
                        echo "<div class='alert alert-success'>$registrationSuccess</div>";
                    }
                    ?>
                    <form method="POST" action="register.php" id="registrationForm">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                        
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input type="text" class="form-control" id="username" name="username" 
                                       placeholder="Choose a username" 
                                       value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" 
                                       required minlength="3">
                            </div>
                        </div>
                        
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
                                       placeholder="Create a strong password" 
                                       required minlength="8">
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="form-text text-muted">
                                Password must be at least 8 characters long
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirm Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                                       placeholder="Confirm your password" 
                                       required minlength="8">
                                <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="terms" required>
                            <label class="form-check-label" for="terms">
                                I agree to the <a href="terms.php">Terms of Service</a> and <a href="privacy.php">Privacy Policy</a>
                            </label>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Create Account</button>
                        </div>
                    </form>
                    
                    <div class="text-center mt-3">
                        <p>Already have an account? <a href="login.php">Login here</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirm_password');
    const togglePasswordBtn = document.getElementById('togglePassword');
    const toggleConfirmPasswordBtn = document.getElementById('toggleConfirmPassword');
    const registrationForm = document.getElementById('registrationForm');

    // Password toggle functionality
    [
        { input: passwordInput, toggleBtn: togglePasswordBtn },
        { input: confirmPasswordInput, toggleBtn: toggleConfirmPasswordBtn }
    ].forEach(({ input, toggleBtn }) => {
        toggleBtn.addEventListener('click', function() {
            const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
            input.setAttribute('type', type);
            
            // Toggle eye icon
            this.querySelector('i').classList.toggle('fa-eye');
            this.querySelector('i').classList.toggle('fa-eye-slash');
        });
    });

    // Client-side form validation
    registrationForm.addEventListener('submit', function(event) {
        const password = passwordInput.value;
        const confirmPassword = confirmPasswordInput.value;
        const termsCheckbox = document.getElementById('terms');

        // Password match validation
        if (password !== confirmPassword) {
            event.preventDefault();
            alert('Passwords do not match');
            return;
        }

        // Terms agreement validation
        if (!termsCheckbox.checked) {
            event.preventDefault();
            alert('Please agree to the Terms of Service and Privacy Policy');
            return;
        }
    });
});
</script>

<?php
// Include footer
require_once 'includes/footer.php';
?> 