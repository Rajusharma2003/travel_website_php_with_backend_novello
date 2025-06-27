<?php
// Include session and config files
require_once "includes/session.php";
require_once "includes/config.php";

// Check if the user is logged in
check_login();

// Fetch all tours from the database
$sql = "SELECT t.*, tc.category_name FROM tours t JOIN tour_categories tc ON t.category_id = tc.category_id ORDER BY t.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$tours = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
</head>
<body>
   
    <!-- Import header here -->
    <?php include "includes/header.php"; ?>

    <div class="container-fluid">
        <div class="row">
           

            <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Manage Tours</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <a href="add_tour.php" class="btn btn-sm btn-success"><i class="fas fa-plus"></i> Add New Tour</a>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-sm">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Image</th> <!-- Added Image column header -->
                                <th>Category</th>
                                <th>Price</th>
                                <th>Duration</th>
                                <th>Location</th>
                                <th>Featured</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (count($tours) > 0) {
                                foreach ($tours as $row) {
                                    echo "<tr>";
                                    echo "<td>" . $row['tour_id'] . "</td>";
                                    echo "<td>" . $row['tour_name'] . "</td>";
                                     // Display the featured image
                                     echo "<td>";
                                     if (!empty($row['featured_image'])) {
                                         echo "<img src='" . htmlspecialchars($row['featured_image']) . "' alt='Tour Image' style='width: 50px; height: 50px; object-fit: cover;'>";
                                     } else {
                                         echo "No Image";
                                     }
                                    echo "<td>" . $row['category_name'] . "</td>";
                                    echo "<td>₹" . number_format($row['price'], 2) . "</td>";
                                    echo "<td>" . $row['duration'] . "</td>";
                                    echo "<td>" . $row['location'] . "</td>";
                                    echo "<td>" . ($row['featured'] ? 'Yes' : 'No') . "</td>";
                                   
                                    echo "</td>";
                                    echo "<td>";
                                    echo "<a href='edit_tour.php?id=" . $row['tour_id'] . "' class='btn btn-sm btn-primary mr-1'><i class='fas fa-edit'></i> Edit</a>";
                                    echo "<a href='delete_tour.php?id=" . $row['tour_id'] . "' class='btn btn-sm btn-danger' onclick='return confirm(\"Are you sure you want to delete this tour?\");'><i class='fas fa-trash'></i> Delete</a>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='9' class='text-center'>No tours found.</td></tr>"; // Updated colspan
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </main>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>