<?php
require_once "includes/session.php";
require_once "includes/config.php";

check_login();

$category_name = '';
$errors = [];

// Handle Add/Edit Category
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add_category'])) {
        $category_name = trim($_POST["category_name"]);

        if (empty($category_name)) {
            $errors[] = "Category name is required.";
        }

        if (empty($errors)) {
            $insert_sql = "INSERT INTO tour_categories (category_name) VALUES (?)";
            if ($stmt = $pdo->prepare($insert_sql)) {
                $stmt->bindParam(1, $category_name);
                if ($stmt->execute()) {
                    header("location: categories.php");
                    exit();
                } else {
                    $errors[] = "Error: Could not execute query. " . $stmt->errorInfo()[2];
                }
            }
        }
    } elseif (isset($_POST['edit_category'])) {
        $category_id = $_POST['category_id'];
        $category_name = trim($_POST["category_name"]);

        if (empty($category_name)) {
            $errors[] = "Category name is required.";
        }

        if (empty($errors)) {
            $update_sql = "UPDATE tour_categories SET category_name = ? WHERE category_id = ?";
            if ($stmt = $pdo->prepare($update_sql)) {
                $stmt->bindParam(1, $category_name);
                $stmt->bindParam(2, $category_id);
                if ($stmt->execute()) {
                    header("location: categories.php");
                    exit();
                } else {
                    $errors[] = "Error: Could not execute query. " . $stmt->errorInfo()[2];
                }
            }
        }
    }
}

// Handle Delete Category
if (isset($_GET['delete_id']) && !empty(trim($_GET['delete_id']))) {
    $delete_id = trim($_GET['delete_id']);
    $delete_sql = "DELETE FROM tour_categories WHERE category_id = ?";
    if ($stmt = $pdo->prepare($delete_sql)) {
        $stmt->bindParam(1, $delete_id);
        if ($stmt->execute()) {
            header("location: categories.php");
            exit();
        } else {
            $errors[] = "Error: Could not delete category. " . $stmt->errorInfo()[2];
        }
    }
}

// Fetch all categories
$sql = "SELECT * FROM tour_categories ORDER BY category_name ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories - Travel Management System</title>
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
                    <h1 class="h2">Manage Categories</h1>
                </div>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php foreach ($errors as $error): ?>
                            <?php echo $error; ?><br>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <div class="mb-3">
                    <h3>Add New Category</h3>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="form-group">
                            <label for="category_name">Category Name</label>
                            <input type="text" class="form-control" id="category_name" name="category_name" value="<?php echo htmlspecialchars($category_name); ?>" required>
                        </div>
                        <button type="submit" name="add_category" class="btn btn-primary">Add Category</button>
                    </form>
                </div>

                <h3>Existing Categories</h3>
                <div class="table-responsive">
                    <table class="table table-striped table-sm">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Category Name</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (count($categories) > 0) {
                                foreach ($categories as $row) {
                                    echo "<tr>";
                                    echo "<td>" . $row['category_id'] . "</td>";
                                    echo "<td>" . $row['category_name'] . "</td>";
                                    echo "<td>";
                                    echo "<a href='#' class='btn btn-sm btn-primary mr-1 edit-category-btn' data-id='" . $row['category_id'] . "' data-name='" . htmlspecialchars($row['category_name']) . "'><i class='fas fa-edit'></i> Edit</a>";
                                    echo "<a href='categories.php?delete_id=" . $row['category_id'] . "' class='btn btn-sm btn-danger' onclick='return confirm(\"Are you sure you want to delete this category?\");'><i class='fas fa-trash'></i> Delete</a>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='3' class='text-center'>No categories found.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <!-- Edit Category Modal -->
                <div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editCategoryModalLabel">Edit Category</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                                <div class="modal-body">
                                    <input type="hidden" id="edit_category_id" name="category_id">
                                    <div class="form-group">
                                        <label for="edit_category_name">Category Name</label>
                                        <input type="text" class="form-control" id="edit_category_name" name="category_name" required>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button type="submit" name="edit_category" class="btn btn-primary">Save changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </main>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.edit-category-btn').on('click', function() {
                var id = $(this).data('id');
                var name = $(this).data('name');
                $('#edit_category_id').val(id);
                $('#edit_category_name').val(name);
                $('#editCategoryModal').modal('show');
            });
        });
    </script>
</body>
</html>