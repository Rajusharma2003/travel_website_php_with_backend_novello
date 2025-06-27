<?php
require_once "includes/session.php";
require_once "includes/config.php";

check_login();

if (isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
    $tour_id = trim($_GET["id"]);

    try {
        // First, retrieve the image path before deleting the tour record
        $sql_select_image = "SELECT featured_image FROM tours WHERE tour_id = :tour_id";
        $stmt_select = $pdo->prepare($sql_select_image);
        $stmt_select->bindParam(":tour_id", $tour_id, PDO::PARAM_INT);
        $stmt_select->execute();
        $featured_image = $stmt_select->fetchColumn();

        // Construct the absolute path to the image file
        $image_path_absolute = __DIR__ . DIRECTORY_SEPARATOR . $featured_image;

        // Delete the image file from the server if it exists and is not empty
        if (!empty($featured_image) && file_exists($image_path_absolute)) {
            unlink($image_path_absolute);
        }

        // Prepare a delete statement
        $sql = "DELETE FROM tours WHERE tour_id = :tour_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":tour_id", $tour_id, PDO::PARAM_INT);

        // Attempt to execute the prepared statement
        if ($stmt->execute()) {
            // Records deleted successfully. Redirect to landing page
            header("location: tours.php");
            exit();
        } else {
            echo "Oops! Something went wrong. Please try again later.";
        }
    } catch (PDOException $e) {
        die("ERROR: Could not able to execute $sql. " . $e->getMessage());
    }
} else {
    // URL doesn't contain id parameter. Redirect to error page or tours page
    header("location: tours.php"); // Or an error page
    exit();
}
?>