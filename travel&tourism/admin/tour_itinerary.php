<?php
session_start();
include 'includes/config.php';

// Check if user is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch available tours
$tours_query = "SELECT tour_id, tour_name FROM tours ORDER BY tour_name";
$tours_result = mysqli_query($conn, $tours_query);

// Handle form submission for adding/editing itinerary
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tour_id = intval($_POST['tour_id']);
    $day_number = intval($_POST['day_number']);
    $title = mysqli_real_escape_string($conn, trim($_POST['title']));
    $description = mysqli_real_escape_string($conn, trim($_POST['description']));

    // Check if this day already exists for the tour
    $check_query = "SELECT * FROM tour_itinerary WHERE tour_id = ? AND day_number = ?";
    $check_stmt = mysqli_prepare($conn, $check_query);
    mysqli_stmt_bind_param($check_stmt, "ii", $tour_id, $day_number);
    mysqli_stmt_execute($check_stmt);
    $check_result = mysqli_stmt_get_result($check_stmt);

    if (mysqli_num_rows($check_result) > 0) {
        // Update existing itinerary day
        $update_query = "UPDATE tour_itinerary SET title = ?, description = ? WHERE tour_id = ? AND day_number = ?";
        $update_stmt = mysqli_prepare($conn, $update_query);
        mysqli_stmt_bind_param($update_stmt, "ssii", $title, $description, $tour_id, $day_number);
        
        if (mysqli_stmt_execute($update_stmt)) {
            $success_message = "Itinerary day updated successfully!";
        } else {
            $error_message = "Failed to update itinerary: " . mysqli_error($conn);
        }
    } else {
        // Insert new itinerary day
        $insert_query = "INSERT INTO tour_itinerary (tour_id, day_number, title, description) VALUES (?, ?, ?, ?)";
        $insert_stmt = mysqli_prepare($conn, $insert_query);
        mysqli_stmt_bind_param($insert_stmt, "iiss", $tour_id, $day_number, $title, $description);
        
        if (mysqli_stmt_execute($insert_stmt)) {
            $success_message = "Itinerary day added successfully!";
        } else {
            $error_message = "Failed to add itinerary: " . mysqli_error($conn);
        }
    }
}

// Handle deletion
if (isset($_GET['delete']) && isset($_GET['id'])) {
    $itinerary_id = intval($_GET['id']);
    
    $delete_query = "DELETE FROM tour_itinerary WHERE itinerary_id = ?";
    $delete_stmt = mysqli_prepare($conn, $delete_query);
    mysqli_stmt_bind_param($delete_stmt, "i", $itinerary_id);
    
    if (mysqli_stmt_execute($delete_stmt)) {
        $success_message = "Itinerary day deleted successfully!";
    } else {
        $error_message = "Failed to delete itinerary: " . mysqli_error($conn);
    }
}

// Fetch existing itineraries
$itinerary_query = "
    SELECT ti.*, t.tour_name 
    FROM tour_itinerary ti
    JOIN tours t ON ti.tour_id = t.tour_id
    ORDER BY t.tour_name, ti.day_number
";
$itinerary_result = mysqli_query($conn, $itinerary_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Tour Itinerary - Travel Management System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="./assets/css/style.css">
</head>
<body>
    <?php include "includes/header.php"; ?>

    <div class="container-fluid">
        <div class="row">
            <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
                <h1 class="mt-4">Manage Tour Itinerary</h1>

                <?php if (isset($error_message)): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
                <?php endif; ?>

                <?php if (isset($success_message)): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
                <?php endif; ?>

                <div class="card mb-4">
                    <div class="card-header">
                        <h2>Add/Edit Itinerary Day</h2>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label for="tour_id">Select Tour</label>
                                    <select class="form-control" id="tour_id" name="tour_id" required>
                                        <option value="">Choose a Tour</option>
                                        <?php 
                                        // Reset the pointer
                                        mysqli_data_seek($tours_result, 0);
                                        while ($tour = mysqli_fetch_assoc($tours_result)): ?>
                                            <option value="<?php echo $tour['tour_id']; ?>">
                                                <?php echo htmlspecialchars($tour['tour_name']); ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="form-group col-md-2">
                                    <label for="day_number">Day Number</label>
                                    <input type="number" class="form-control" id="day_number" name="day_number" min="1" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="title">Day Title</label>
                                <input type="text" class="form-control" id="title" name="title" required>
                            </div>

                            <div class="form-group">
                                <label for="description">Day Description</label>
                                <textarea class="form-control" id="description" name="description" rows="5" required></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary">Save Itinerary Day</button>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h2>Existing Itineraries</h2>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Tour Name</th>
                                        <th>Day</th>
                                        <th>Title</th>
                                        <th>Description</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($itinerary = mysqli_fetch_assoc($itinerary_result)): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($itinerary['tour_name']); ?></td>
                                            <td><?php echo htmlspecialchars($itinerary['day_number']); ?></td>
                                            <td><?php echo htmlspecialchars($itinerary['title']); ?></td>
                                            <td><?php echo nl2br(htmlspecialchars($itinerary['description'])); ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-warning edit-itinerary" 
                                                    data-tour-id="<?php echo $itinerary['tour_id']; ?>"
                                                    data-day-number="<?php echo $itinerary['day_number']; ?>"
                                                    data-title="<?php echo htmlspecialchars($itinerary['title']); ?>"
                                                    data-description="<?php echo htmlspecialchars($itinerary['description']); ?>">
                                                    <i class="fas fa-edit"></i> Edit
                                                </button>
                                                <a href="?delete=1&id=<?php echo $itinerary['itinerary_id']; ?>" 
                                                   class="btn btn-sm btn-danger" 
                                                   onclick="return confirm('Are you sure you want to delete this itinerary day?');">
                                                    <i class="fas fa-trash"></i> Delete
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
    $(document).ready(function() {
        // Edit button functionality
        $('.edit-itinerary').click(function() {
            var tourId = $(this).data('tour-id');
            var dayNumber = $(this).data('day-number');
            var title = $(this).data('title');
            var description = $(this).data('description');

            // Set form values
            $('#tour_id').val(tourId);
            $('#day_number').val(dayNumber);
            $('#title').val(title);
            $('#description').val(description);

            // Scroll to form
            $('html, body').animate({
                scrollTop: $('form').offset().top
            }, 500);
        });
    });
    </script>
</body>
</html>
<?php
// Close database connection
mysqli_close($conn);
?> 