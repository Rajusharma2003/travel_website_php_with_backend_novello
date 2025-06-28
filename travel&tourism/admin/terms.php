<?php
require_once "includes/session.php";
require_once "includes/config.php";

check_login();

$success_message = $error_message = '';
$terms = '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $terms = trim($_POST['terms'] ?? '');
    try {
        // Check if a row exists
        $sql = "SELECT id FROM terms_and_conditions LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            // Update existing
            $sql = "UPDATE terms_and_conditions SET content = :terms WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':terms', $terms);
            $stmt->bindValue(':id', $row['id'], PDO::PARAM_INT);
            $stmt->execute();
        } else {
            // Insert new
            $sql = "INSERT INTO terms_and_conditions (content) VALUES (:terms)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':terms', $terms);
            $stmt->execute();
        }
        $success_message = "Terms and Conditions updated successfully.";
    } catch (PDOException $e) {
        $error_message = "Error updating terms: " . $e->getMessage();
    }
} else {
    // Fetch current terms
    try {
        $sql = "SELECT content FROM terms_and_conditions LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $terms = $stmt->fetchColumn() ?: '';
    } catch (PDOException $e) {
        $error_message = "Error fetching terms: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms and Conditions</title>
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
                    <h2>Terms and Conditions</h2>
                </div>
                <?php if ($success_message): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
                <?php endif; ?>
                <?php if ($error_message): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
                <?php endif; ?>
                <form method="post" class="col-md-8 p-0">
                    <div class="form-group">
                        <label for="terms">Edit Terms and Conditions</label>
                        <textarea class="form-control" id="terms" name="terms" rows="12" required><?php echo htmlspecialchars($terms); ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Terms</button>
                </form>
            </main>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html> 