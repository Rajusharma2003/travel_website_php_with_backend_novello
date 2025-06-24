<?php
require_once "includes/session.php";
require_once "includes/config.php";

check_login();

if (isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
    $tour_id = trim($_GET["id"]);

    // First, retrieve the image path before deleting the tour record
    $sql_select_image = "SELECT featured_image FROM tours WHERE tour_id = ?";
    if ($stmt_select = mysqli_prepare($conn, $sql_select_image)) {
        mysqli_stmt_bind_param($stmt_select, "i", $tour_id);
        if (mysqli_stmt_execute($stmt_select)) {
            mysqli_stmt_bind_result($stmt_select, $featured_image);
            mysqli_stmt_fetch($stmt_select);
            mysqli_stmt_close($stmt_select);

            // Construct the absolute path to the image file
            $image_path_absolute = __DIR__ . DIRECTORY_SEPARATOR . $featured_image;

            // Delete the image file from the server if it exists and is not empty
            if (!empty($featured_image) && file_exists($image_path_absolute)) {
                unlink($image_path_absolute);
            }
        }
    }

    // Prepare a delete statement
    $sql = "DELETE FROM tours WHERE tour_id = ?";

    if ($stmt = mysqli_prepare($conn, $sql)) {
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "i", $tour_id);

        // Attempt to execute the prepared statement
        if (mysqli_stmt_execute($stmt)) {
            // Records deleted successfully. Redirect to landing page
            header("location: tours.php");
            exit();
        } else {
            echo "Oops! Something went wrong. Please try again later.";
        }
    }

    // Close statement
    mysqli_stmt_close($stmt);

    // Close connection
    mysqli_close($conn);
} else {
    // URL doesn't contain id parameter. Redirect to error page or tours page
    header("location: tours.php"); // Or an error page
    exit();
}
?>