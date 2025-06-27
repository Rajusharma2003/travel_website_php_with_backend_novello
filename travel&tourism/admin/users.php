<?php
require_once "includes/session.php";
require_once "includes/config.php";

check_login();

// Fetch all users from the database
$sql = "SELECT user_id, username, email FROM users";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar is included in header.php, so no need to include it here directly -->

            <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h2>Manage Users</h2>
                    <a href="add_user.php" class="btn btn-success"><i class="fas fa-plus"></i> Add New User</a>
                </div>

                <?php
                if (isset($_SESSION['success_message'])) {
                    echo '<div class="alert alert-success">' . $_SESSION['success_message'] . '</div>';
                    unset($_SESSION['success_message']);
                }
                if (isset($_SESSION['error_message'])) {
                    echo '<div class="alert alert-danger">' . $_SESSION['error_message'] . '</div>';
                    unset($_SESSION['error_message']);
                }
                ?>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($users)):
                                foreach ($users as $user):
                                    echo "<tr>";
                                    echo "<td>" . $user['user_id'] . "</td>";
                                    echo "<td>" . $user['username'] . "</td>";
                                    echo "<td>" . $user['email'] . "</td>";
                                    echo "<td>
                                            <a href='edit_user.php?id=" . $user['user_id'] . "' class='btn btn-sm btn-primary'><i class='fas fa-edit'></i> Edit</a>
                                            <a href='delete_user.php?id=" . $user['user_id'] . "' class='btn btn-sm btn-danger' onclick='return confirm(\"Are you sure you want to delete this user?\");'><i class='fas fa-trash'></i> Delete</a>
                                        </td>";
                                    echo "</tr>";
                                endforeach;
                            else:
                                echo "<tr>
                                    <td colspan='4'>No users found.</td>
                                </tr>";
                            endif; ?>
                        </tbody>
                    </table>
                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>