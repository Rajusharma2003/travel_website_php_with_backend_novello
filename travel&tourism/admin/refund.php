<?php
require_once "includes/session.php";
require_once "includes/config.php";

check_login();

$success_message = $error_message = '';
$refund = '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $refund = trim($_POST['refund'] ?? '');
    try {
        // Check if a row exists
        $sql = "SELECT id FROM refund_policy LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            // Update existing
            $sql = "UPDATE refund_policy SET content = :refund WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':refund', $refund);
            $stmt->bindValue(':id', $row['id'], PDO::PARAM_INT);
            $stmt->execute();
        } else {
            // Insert new
            $sql = "INSERT INTO refund_policy (content) VALUES (:refund)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':refund', $refund);
            $stmt->execute();
        }
        $success_message = "Refund Policy updated successfully.";
    } catch (PDOException $e) {
        $error_message = "Error updating refund policy: " . $e->getMessage();
    }
} else {
    // Fetch current refund policy
    try {
        $sql = "SELECT content FROM refund_policy LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $refund = $stmt->fetchColumn() ?: '';
    } catch (PDOException $e) {
        $error_message = "Error fetching refund policy: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Refund Policy</title>
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
                    <h2>Refund Policy</h2>
                </div>
                <?php if ($success_message): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
                <?php endif; ?>
                <?php if ($error_message): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
                <?php endif; ?>
                <form method="post" class="col-md-8 p-0">
                    <div class="form-group">
                        <label for="refund">Edit Refund Policy</label>
                        <textarea class="form-control" id="refund" name="refund" rows="12" required><?php echo htmlspecialchars($refund); ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Policy</button>
                </form>
            </main>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html> 