<?php
require_once "includes/session.php";
require_once "includes/config.php";

check_login();

$user_id = $username = $email = "";
$username_err = $email_err = $password_err = $confirm_password_err = "";

// Process GET request for user data
if (isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
    $user_id = trim($_GET["id"]);

    try {
        $sql = "SELECT username, email FROM users WHERE user_id = :user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $username = $row["username"];
            $email = $row["email"];
        } else {
            $_SESSION['error_message'] = "User not found.";
            header("location: users.php");
            exit();
        }
    } catch (PDOException $e) {
        echo "Oops! Something went wrong. Please try again later. " . $e->getMessage();
    }
} else {
    $_SESSION['error_message'] = "Invalid user ID.";
    header("location: users.php");
    exit();
}

// Process POST request for updating user data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_POST["user_id"];

    // Validate username
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter a username.";
    } else {
        try {
            $sql = "SELECT user_id FROM users WHERE username = :username AND user_id != :user_id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->bindParam(":user_id", $param_id, PDO::PARAM_INT);
            $param_username = trim($_POST["username"]);
            $param_id = $user_id;
            $stmt->execute();
            if ($stmt->rowCount() == 1) {
                $username_err = "This username is already taken.";
            } else {
                $username = trim($_POST["username"]);
            }
        } catch (PDOException $e) {
            echo "Oops! Something went wrong. Please try again later. " . $e->getMessage();
        }
    }

    // Validate email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter an email.";
    } else {
        try {
            $sql = "SELECT user_id FROM users WHERE email = :email AND user_id != :user_id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(":email", $param_email, PDO::PARAM_STR);
            $stmt->bindParam(":user_id", $param_id, PDO::PARAM_INT);
            $param_email = trim($_POST["email"]);
            $param_id = $user_id;
            $stmt->execute();
            if ($stmt->rowCount() == 1) {
                $email_err = "This email is already registered.";
            } else {
                $email = trim($_POST["email"]);
            }
        } catch (PDOException $e) {
            echo "Oops! Something went wrong. Please try again later. " . $e->getMessage();
        }
    }

    // Validate password if provided
    $password_update = false;
    if (!empty(trim($_POST["password"]))) {
        $password_update = true;
        if (strlen(trim($_POST["password"])) < 6) {
            $password_err = "Password must have at least 6 characters.";
        } else {
            $password = trim($_POST["password"]);
        }

        if (empty(trim($_POST["confirm_password"]))) {
            $confirm_password_err = "Please confirm password.";
        } else {
            $confirm_password = trim($_POST["confirm_password"]);
            if (empty($password_err) && ($password != $confirm_password)) {
                $confirm_password_err = "Password did not match.";
            }
        }
    }

    // Check input errors before updating in database
    if (empty($username_err) && empty($email_err) && empty($password_err) && empty($confirm_password_err)) {
        try {
            if ($password_update) {
                $sql = "UPDATE users SET username = :username, email = :email, password = :password WHERE user_id = :user_id";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(":username", $username, PDO::PARAM_STR);
                $stmt->bindParam(":email", $email, PDO::PARAM_STR);
                $param_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt->bindParam(":password", $param_password, PDO::PARAM_STR);
                $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
            } else {
                $sql = "UPDATE users SET username = :username, email = :email WHERE user_id = :user_id";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(":username", $username, PDO::PARAM_STR);
                $stmt->bindParam(":email", $email, PDO::PARAM_STR);
                $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
            }

            if ($stmt->execute()) {
                $_SESSION['success_message'] = "User updated successfully.";
                header("location: users.php");
                exit();
            } else {
                $_SESSION['error_message'] = "Error: Could not update user. Please try again later.";
            }
        } catch (PDOException $e) {
            $_SESSION['error_message'] = "Error: Could not update user. " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
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
            <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Edit User</h1>
                </div>

                <div class="col-md-6 mx-auto">
                    <div class="card">
                        <div class="card-header">
                            Edit User Details
                        </div>
                        <div class="card-body">
                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>?id=<?php echo $user_id; ?>" method="post">
                                <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
                                <div class="form-group">
                                    <label>Username</label>
                                    <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
                                    <span class="invalid-feedback"><?php echo $username_err; ?></span>
                                </div>
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" name="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $email; ?>">
                                    <span class="invalid-feedback"><?php echo $email_err; ?></span>
                                </div>
                                <div class="form-group">
                                    <label>New Password (leave blank to keep current)</label>
                                    <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
                                    <span class="invalid-feedback"><?php echo $password_err; ?></span>
                                </div>
                                <div class="form-group">
                                    <label>Confirm New Password</label>
                                    <input type="password" name="confirm_password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>">
                                    <span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
                                </div>
                                <div class="form-group">
                                    <input type="submit" class="btn btn-primary" value="Update">
                                    <a href="users.php" class="btn btn-secondary">Cancel</a>
                                </div>
                            </form>
                        </div>
                    </div>
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