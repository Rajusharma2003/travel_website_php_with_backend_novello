<?php
// Start session for user authentication
session_start();

// Define site root to prevent direct access to include files
defined('SITE_ROOT') or define('SITE_ROOT', true);

// Set default page title if not provided
$pageTitle = isset($pageTitle) ? $pageTitle : "Trae Travel";

// Include configuration and database connection
require_once 'config.php';
require_once 'db_connection.php';
require_once 'functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?> - Trae Travel</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">
    
    <!-- Owl Carousel CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">
    <!-- Font Awesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    
    <!-- Additional page-specific styles can be added dynamically -->
    <?php if(isset($additionalStyles)): ?>
        <?php foreach($additionalStyles as $style): ?>
            <link rel="stylesheet" href="<?php echo htmlspecialchars($style); ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img src="./assets/images/logo.png" alt="Trae Travel Logo" height="100" width="120">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <!-- <li class="nav-item">
                        <a class="nav-link" href="tours/browse_tours.php">Tours</a>
                    </li> -->
                    <li class="nav-item">
                        <a class="nav-link" href="packages.php">Packages</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="gallery.php">Gallery</a>
                    </li>   
                    <li class="nav-item">
                        <a class="nav-link" href="about.php">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.php">Contact</a>
                    </li>
                </ul>
                <div class="navbar-nav">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <div class="dropdown">
                            <a class="btn btn-outline-primary dropdown-toggle me-2" href="#" role="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <?php echo htmlspecialchars($_SESSION['usernam'] ?? 'My Account'); ?>
                            </a>
                            
                            <ul class="dropdown-menu" aria-labelledby="userDropdown">
                                
                                <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                                    <li><a class="dropdown-item" href="../admin/index.php">Admin Dashboard</a></li>
                                <?php endif; ?>
                                <li><a class="dropdown-item" href="profile.php">Profile</a></li>
                                <li><a class="dropdown-item" href="bookings.php">My Bookings</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-outline-primary me-2">Login</a>
                        <a href="register.php" class="btn btn-primary">Register</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content Container -->
    <div class="container-fluid p-0">
        <!-- Page-specific content will be inserted here -->
    </div>
</body>
</html> 