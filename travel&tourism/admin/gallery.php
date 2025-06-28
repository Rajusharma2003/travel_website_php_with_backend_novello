<?php
session_start();
include 'includes/config.php';

// Verify database connection
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Check if user is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Handle image upload
if (isset($_POST['upload_gallery'])) {
    // Validate inputs
    $title = mysqli_real_escape_string($conn, trim($_POST['title']));
    $description = mysqli_real_escape_string($conn, trim($_POST['description'] ?? ''));
    
    if (empty($title)) {
        $error_message = "Title cannot be empty.";
    } elseif (isset($_FILES['gallery_image']) && $_FILES['gallery_image']['error'] == 0) {
        $upload_dir = '../uploads/gallery/';
        
        // Create upload directory if it doesn't exist
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        // Generate unique filename
        $file_extension = strtolower(pathinfo($_FILES['gallery_image']['name'], PATHINFO_EXTENSION));
        $unique_filename = 'gallery_' . uniqid() . '.' . $file_extension;
        $upload_path = $upload_dir . $unique_filename;
        
        // Check file type
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (in_array($file_extension, $allowed_types)) {
            if (move_uploaded_file($_FILES['gallery_image']['tmp_name'], $upload_path)) {
                // Insert gallery image info into database
                $image_path = 'uploads/gallery/' . $unique_filename;
                $insert_query = "INSERT INTO gallery (title, description, image_path) VALUES (?, ?, ?)";
                
                // Use prepared statement to prevent SQL injection
                $stmt = mysqli_prepare($conn, $insert_query);
                mysqli_stmt_bind_param($stmt, "sss", $title, $description, $image_path);
                
                if (mysqli_stmt_execute($stmt)) {
                    $success_message = "Image uploaded successfully!";
                } else {
                    // If DB insert fails, remove the uploaded file
                    unlink($upload_path);
                    $error_message = "Failed to save image to database: " . mysqli_error($conn);
                }
                
                mysqli_stmt_close($stmt);
            } else {
                $error_message = "Failed to upload image.";
            }
        } else {
            $error_message = "Invalid file type. Only JPG, PNG, and GIF are allowed.";
        }
    } else {
        $error_message = "No image uploaded or upload error occurred.";
    }
}

// Handle image deletion
if (isset($_GET['delete']) && isset($_GET['id'])) {
    $gallery_id = intval($_GET['id']);
    
    // Use prepared statement for deletion
    $select_query = "SELECT image_path FROM gallery WHERE gallery_id = ?";
    $stmt = mysqli_prepare($conn, $select_query);
    mysqli_stmt_bind_param($stmt, "i", $gallery_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($row = mysqli_fetch_assoc($result)) {
        $file_to_delete = '../' . $row['image_path'];
        
        // Delete from database using prepared statement
        $delete_query = "DELETE FROM gallery WHERE gallery_id = ?";
        $delete_stmt = mysqli_prepare($conn, $delete_query);
        mysqli_stmt_bind_param($delete_stmt, "i", $gallery_id);
        
        if (mysqli_stmt_execute($delete_stmt)) {
            // Delete physical file if it exists
            if (file_exists($file_to_delete)) {
                unlink($file_to_delete);
            }
            $success_message = "Image deleted successfully!";
        } else {
            $error_message = "Failed to delete image from database: " . mysqli_error($conn);
        }
        
        mysqli_stmt_close($delete_stmt);
    }
    
    mysqli_stmt_close($stmt);
}

// Get list of gallery images
$gallery_query = "SELECT * FROM gallery ORDER BY created_at DESC";
$gallery_result = mysqli_query($conn, $gallery_query);

// Check if query was successful
if ($gallery_result === false) {
    $error_message = "Failed to retrieve gallery images: " . mysqli_error($conn);
    $gallery_result = []; // Ensure it's an empty array if query fails
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Tours - Travel Management System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="./assets/css/style.css">
    <style>
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 15px;
            padding: 20px;
        }
        .gallery-item {
            position: relative;
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }
        .gallery-item img {
            max-width: 100%;
            max-height: 250px;
            object-fit: cover;
        }
        .delete-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: red;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <?php include "includes/header.php"; ?>

    <div class="container-fluid">
        <div class="row">
            <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
                <h1>Gallery Management</h1>
                
                <?php if (isset($error_message)): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
                <?php endif; ?>
                
                <?php if (isset($success_message)): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
                <?php endif; ?>
                
                <div class="card mb-4">
                    <div class="card-header">
                        <h2>Upload New Image</h2>
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="title">Image Title</label>
                                <input type="text" class="form-control" id="title" name="title" required>
                            </div>
                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="gallery_image">Choose Image</label>
                                <input type="file" class="form-control-file" id="gallery_image" name="gallery_image" accept="image/jpeg,image/png,image/gif" required>
                            </div>
                            <button type="submit" name="upload_gallery" class="btn btn-primary">Upload Image</button>
                        </form>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h2>Existing Gallery Images</h2>
                    </div>
                    <div class="card-body">
                        <div class="gallery-grid">
                            <?php if (mysqli_num_rows($gallery_result) == 0): ?>
                                <p class="text-center">No images in the gallery yet.</p>
                            <?php else: ?>
                                <?php while ($image = mysqli_fetch_assoc($gallery_result)): ?>
                                    <div class="gallery-item">
                                        <img src="../<?php echo htmlspecialchars($image['image_path']); ?>" alt="<?php echo htmlspecialchars($image['title']); ?>">
                                        <div class="mt-2">
                                            <strong><?php echo htmlspecialchars($image['title']); ?></strong>
                                            <p><?php echo htmlspecialchars($image['description'] ?? 'No description'); ?></p>
                                        </div>
                                        <a href="?delete=1&id=<?php echo $image['gallery_id']; ?>" 
                                           class="btn btn-danger btn-sm delete-btn" 
                                           onclick="return confirm('Are you sure you want to delete this image?');">
                                            <i class="fas fa-trash"></i> Delete
                                        </a>
                                    </div>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
<?php
// Close the database connection
mysqli_close($conn);
?>
