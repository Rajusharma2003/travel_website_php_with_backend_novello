<?php
require_once "includes/session.php";
require_once "includes/config.php";

check_login();

if (isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
    $user_id = trim($_GET["id"]);

    // Prepare a delete statement
    $sql = "DELETE FROM users WHERE user_id = ?";

    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $param_id);
        $param_id = $user_id;

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success_message'] = "User deleted successfully.";
        } else {
            $_SESSION['error_message'] = "Error: Could not delete user. Please try again later.";
        }
        mysqli_stmt_close($stmt);
    }
    mysqli_close($conn);
} else {
    $_SESSION['error_message'] = "Invalid user ID.";
}

header("location: users.php");
exit();
?>