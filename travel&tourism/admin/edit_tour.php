<?php
require_once "includes/session.php";
require_once "includes/config.php";

check_login();

$tour_id = $_GET['id'] ?? null;

if (!$tour_id) {
    header("location: tours.php");
    exit();
}

$tour_name = $description = $price = $duration = $location = $category_id = '';
$featured = 0;
$errors = [];

// Fetch categories for the dropdown
$categories_sql = "SELECT category_id, category_name FROM tour_categories ORDER BY category_name ASC";
$categories_result = mysqli_query($conn, $categories_sql);

if (!$categories_result) {
    die("Error fetching categories: " . mysqli_error($conn));
}

// Fetch tour data for editing
$sql = "SELECT * FROM tours WHERE tour_id = ?";
if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $tour_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if (mysqli_num_rows($result) == 1) {
        $tour = mysqli_fetch_assoc($result);
        $tour_name = $tour['tour_name'];
        $description = $tour['description'];
        $price = $tour['price'];
        $duration = $tour['duration'];
        $location = $tour['location'];
        $category_id = $tour['category_id'];
        $featured = $tour['featured'];
        $featured_image = $tour['featured_image']; // Fetch existing image path
    } else {
        header("location: tours.php");
        exit();
    }
    mysqli_stmt_close($stmt);
} else {
    die("Error: Could not prepare query. " . mysqli_error($conn));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tour_name = trim($_POST["tour_name"]);
    $description = trim($_POST["description"]);
    $price = trim($_POST["price"]);
    $duration = trim($_POST["duration"]);
    $location = trim($_POST["location"]);
    $category_id = trim($_POST["category_id"]);
    $featured = isset($_POST["featured"]) ? 1 : 0;

    // Image upload handling
    $new_featured_image = $featured_image; // Keep existing image by default
    if (isset($_FILES["featured_image"]) && $_FILES["featured_image"]["error"] == UPLOAD_ERR_OK) {
        $target_dir_relative = "assets/images/tours/";
        $upload_dir_absolute = __DIR__ . DIRECTORY_SEPARATOR . $target_dir_relative;

        // Create directory if it doesn't exist
        if (!is_dir($upload_dir_absolute)) {
            mkdir($upload_dir_absolute, 0777, true);
        }

        $file_name = basename($_FILES["featured_image"]["name"]);
        $target_file_absolute = $upload_dir_absolute . $file_name;
        $imageFileType = strtolower(pathinfo($target_file_absolute, PATHINFO_EXTENSION));

        // Check if image file is a actual image or fake image
        $check = getimagesize($_FILES["featured_image"]["tmp_name"]);
        if ($check === false) {
            $errors[] = "File is not an image.";
        }

        // Check file size (e.g., 5MB limit)
        if ($_FILES["featured_image"]["size"] > 5000000) {
            $errors[] = "Sorry, your file is too large.";
        }

        // Allow certain file formats
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
            && $imageFileType != "gif") {
            $errors[] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        }

        // Check if $errors is empty before attempting to upload
        if (empty($errors)) {
            if (move_uploaded_file($_FILES["featured_image"]["tmp_name"], $target_file_absolute)) {
                $new_featured_image = $target_dir_relative . $file_name; // Store relative path in DB
            } else {
                $errors[] = "Sorry, there was an error uploading your file.";
            }
        }
    }

    // Basic validation
    if (empty($tour_name)) {
        $errors[] = "Tour name is required.";
    }
    if (empty($description)) {
        $errors[] = "Description is required.";
    }
    if (empty($price) || !is_numeric($price) || $price < 0) {
        $errors[] = "Valid price is required.";
    }
    if (empty($duration)) {
        $errors[] = "Duration is required.";
    }
    if (empty($location)) {
        $errors[] = "Location is required.";
    }
    if (empty($category_id)) {
        $errors[] = "Category is required.";
    }

    if (empty($errors)) {
        $update_sql = "UPDATE tours SET tour_name = ?, description = ?, price = ?, duration = ?, location = ?, category_id = ?, featured = ?, featured_image = ? WHERE tour_id = ?";
        if ($stmt = mysqli_prepare($conn, $update_sql)) {
            mysqli_stmt_bind_param($stmt, "ssdssiisi", $tour_name, $description, $price, $duration, $location, $category_id, $featured, $new_featured_image, $tour_id);
            if (mysqli_stmt_execute($stmt)) {
                header("location: tours.php");
                exit();
            } else {
                $errors[] = "Error: Could not execute query. " . mysqli_error($conn);
            }
            mysqli_stmt_close($stmt);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Tour - Travel Management System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="./assets/css/style.css">
</head>
<body>
     <!-- Import header here -->
     <?php include "includes/header.php"; ?>

    <div class="container-fluid">
        <div class="row">
            <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Edit Tour</h1>
                </div>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php foreach ($errors as $error): ?>
                            <?php echo $error; ?><br>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?id=' . $tour_id; ?>" method="post" enctype="multipart/form-data"> <!-- Added enctype -->
                    <div class="form-group">
                        <label for="tour_name">Tour Name</label>
                        <input type="text" class="form-control" id="tour_name" name="tour_name" value="<?php echo htmlspecialchars($tour_name); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="5" required><?php echo htmlspecialchars($description); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="price">Price</label>
                        <input type="number" step="0.01" class="form-control" id="price" name="price" value="<?php echo htmlspecialchars($price); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="duration">Duration</label>
                        <input type="text" class="form-control" id="duration" name="duration" value="<?php echo htmlspecialchars($duration); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="location">Location</label>
                        <input type="text" class="form-control" id="location" name="location" value="<?php echo htmlspecialchars($location); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="category_id">Category</label>
                        <select class="form-control" id="category_id" name="category_id" required>
                            <option value="">Select a category</option>
                            <?php while ($category = mysqli_fetch_assoc($categories_result)): ?>
                                <option value="<?php echo $category['category_id']; ?>" <?php echo ($category['category_id'] == $category_id) ? 'selected' : ''; ?>><?php echo htmlspecialchars($category['category_name']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="featured_image">Featured Image</label>
                        <?php if (!empty($featured_image)): ?>
                            <div class="mb-2">
                                Current Image: <br>
                                <img src="<?php echo htmlspecialchars($featured_image); ?>" alt="Current Tour Image" style="max-width: 200px; height: auto;">
                            </div>
                        <?php endif; ?>
                        <input type="file" class="form-control-file" id="featured_image" name="featured_image">
                        <small class="form-text text-muted">Upload a new image to replace the current one (Max 5MB, JPG, JPEG, PNG, GIF).</small>
                    </div>
                    <div class="form-group form-check">
                        <input type="checkbox" class="form-check-input" id="featured" name="featured" value="1" <?php echo ($featured == 1) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="featured">Featured Tour</label>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Tour</button>
                    <a href="tours.php" class="btn btn-secondary">Cancel</a>
                </form>
            </main>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>