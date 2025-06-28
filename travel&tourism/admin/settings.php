<?php
require_once "includes/session.php";
require_once "includes/config.php";

check_login();

$success_message = $error_message = '';

// Get current admin info
$admin_id = $_SESSION['admin_id'] ?? null;
$admin_email = '';

if ($admin_id) {
    try {
        $sql = "SELECT email FROM users WHERE user_id = :admin_id AND is_admin = 1";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
        $stmt->execute();
        $admin_email = $stmt->fetchColumn();
    } catch (PDOException $e) {
        $error_message = "Error fetching admin info: " . $e->getMessage();
    }
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $new_email = trim($_POST['admin_email'] ?? '');
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($new_email)) {
        $error_message = "Email cannot be empty.";
    } elseif (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email format.";
    } elseif (!empty($new_password) && strlen($new_password) < 6) {
        $error_message = "Password must be at least 6 characters.";
    } elseif ($new_password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    } else {
        try {
            // Update email and/or password
            if (!empty($new_password)) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $sql = "UPDATE users SET email = :email, password = :password WHERE user_id = :admin_id AND is_admin = 1";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':email', $new_email);
                $stmt->bindParam(':password', $hashed_password);
                $stmt->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
            } else {
                $sql = "UPDATE users SET email = :email WHERE user_id = :admin_id AND is_admin = 1";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':email', $new_email);
                $stmt->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
            }
            $stmt->execute();
            $success_message = "Admin settings updated successfully.";
            $admin_email = $new_email;
        } catch (PDOException $e) {
            $error_message = "Error updating admin settings: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Settings</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <div class="container-fluid">
        <div class="row">
            <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h2>Admin Settings</h2>
                </div>
                <?php if ($success_message): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
                <?php endif; ?>
                <?php if ($error_message): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
                <?php endif; ?>
                <form method="post" class="col-md-6 p-0">
                    <div class="form-group">
                        <label for="admin_email">Admin Email</label>
                        <input type="email" class="form-control" id="admin_email" name="admin_email" value="<?php echo htmlspecialchars($admin_email); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="new_password">New Password <small>(leave blank to keep current password)</small></label>
                        <input type="password" class="form-control" id="new_password" name="new_password" autocomplete="new-password">
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirm New Password</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" autocomplete="new-password">
                    </div>
                    <button type="submit" class="btn btn-primary">Update Settings</button>
                </form>
            </main>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html> 