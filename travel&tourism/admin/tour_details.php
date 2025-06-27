<?php
session_start();
include 'includes/config.php';

// Check if user is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch available tours
$tours_query = "SELECT tour_id, tour_name FROM tours";
$tours_result = mysqli_query($conn, $tours_query);

// Handle form submission for adding/editing tour details
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tour_id = intval($_POST['tour_id']);
    $inclusion = mysqli_real_escape_string($conn, trim($_POST['inclusion']));
    $exclusion = mysqli_real_escape_string($conn, trim($_POST['exclusion']));

    // Check if details already exist for this tour
    $check_query = "SELECT * FROM tour_details WHERE tour_id = ?";
    $check_stmt = mysqli_prepare($conn, $check_query);
    mysqli_stmt_bind_param($check_stmt, "i", $tour_id);
    mysqli_stmt_execute($check_stmt);
    $check_result = mysqli_stmt_get_result($check_stmt);

    if (mysqli_num_rows($check_result) > 0) {
        // Update existing details
        $update_query = "UPDATE tour_details SET inclusion = ?, exclusion = ? WHERE tour_id = ?";
        $update_stmt = mysqli_prepare($conn, $update_query);
        mysqli_stmt_bind_param($update_stmt, "ssi", $inclusion, $exclusion, $tour_id);
        
        if (mysqli_stmt_execute($update_stmt)) {
            $success_message = "Tour details updated successfully!";
        } else {
            $error_message = "Failed to update tour details: " . mysqli_error($conn);
        }
    } else {
        // Insert new details
        $insert_query = "INSERT INTO tour_details (tour_id, inclusion, exclusion) VALUES (?, ?, ?)";
        $insert_stmt = mysqli_prepare($conn, $insert_query);
        mysqli_stmt_bind_param($insert_stmt, "iss", $tour_id, $inclusion, $exclusion);
        
        if (mysqli_stmt_execute($insert_stmt)) {
            $success_message = "Tour details added successfully!";
        } else {
            $error_message = "Failed to add tour details: " . mysqli_error($conn);
        }
    }
}

// Fetch existing tour details
$details_query = "
    SELECT td.detail_id, td.tour_id, td.inclusion, td.exclusion, t.tour_name 
    FROM tour_details td
    JOIN tours t ON td.tour_id = t.tour_id
    ORDER BY t.tour_name
";
$details_result = mysqli_query($conn, $details_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Tour Details - Travel Management System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="./assets/css/style.css">
</head>
<body>
    <?php include "includes/header.php"; ?>

    <div class="container-fluid">
        <div class="row">
            <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
                <h1 class="mt-4">Manage Tour Details</h1>

                <?php if (isset($error_message)): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
                <?php endif; ?>

                <?php if (isset($success_message)): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
                <?php endif; ?>

                <div class="card mb-4">
                    <div class="card-header">
                        <h2>Add/Edit Tour Details</h2>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <div class="form-group">
                                <label for="tour_id">Select Tour</label>
                                <select class="form-control" id="tour_id" name="tour_id" required>
                                    <option value="">Choose a Tour</option>
                                    <?php while ($tour = mysqli_fetch_assoc($tours_result)): ?>
                                        <option value="<?php echo $tour['tour_id']; ?>">
                                            <?php echo htmlspecialchars($tour['tour_name']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="inclusion">Inclusions</label>
                                <textarea class="form-control" id="inclusion" name="inclusion" rows="5" placeholder="List what is included in the tour (one per line)"></textarea>
                            </div>

                            <div class="form-group">
                                <label for="exclusion">Exclusions</label>
                                <textarea class="form-control" id="exclusion" name="exclusion" rows="5" placeholder="List what is not included in the tour (one per line)"></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary">Save Tour Details</button>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h2>Existing Tour Details</h2>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Tour Name</th>
                                        <th>Inclusions</th>
                                        <th>Exclusions</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($detail = mysqli_fetch_assoc($details_result)): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($detail['tour_name']); ?></td>
                                            <td><?php echo nl2br(htmlspecialchars($detail['inclusion'] ?? 'N/A')); ?></td>
                                            <td><?php echo nl2br(htmlspecialchars($detail['exclusion'] ?? 'N/A')); ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-warning edit-details" 
                                                    data-tour-id="<?php echo $detail['tour_id']; ?>"
                                                    data-inclusion="<?php echo htmlspecialchars($detail['inclusion']); ?>"
                                                    data-exclusion="<?php echo htmlspecialchars($detail['exclusion']); ?>">
                                                    <i class="fas fa-edit"></i> Edit
                                                </button>
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
        $('.edit-details').click(function() {
            var tourId = $(this).data('tour-id');
            var inclusion = $(this).data('inclusion');
            var exclusion = $(this).data('exclusion');

            // Set form values
            $('#tour_id').val(tourId);
            $('#inclusion').val(inclusion);
            $('#exclusion').val(exclusion);

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