<?php
require_once "includes/session.php";
require_once "includes/config.php";

check_login();

$admin_id = $_SESSION['admin_id'] ?? null;
$admin = null;
$error_message = '';

if ($admin_id) {
    try {
        $sql = "SELECT username, email FROM users WHERE user_id = :admin_id AND is_admin = 1";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
        $stmt->execute();
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error_message = "Error fetching admin profile: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile</title>
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
                    <h2>Admin Profile</h2>
                </div>
                <?php if ($error_message): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
                <?php elseif ($admin): ?>
                    <div class="card col-md-6 p-0">
                        <div class="card-body">
                            <h5 class="card-title"><i class="fas fa-user"></i> <?php echo htmlspecialchars($admin['username']); ?></h5>
                            <p class="card-text"><strong>Email:</strong> <?php echo htmlspecialchars($admin['email']); ?></p>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning">Admin profile not found.</div>
                <?php endif; ?>
            </main>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html> 