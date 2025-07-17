<?php
session_start();

defined('SITE_ROOT') or define('SITE_ROOT', true);

require_once 'includes/db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = getDbConnection();

    $name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $subject = htmlspecialchars(trim($_POST['subject']));
    $message = htmlspecialchars(trim($_POST['message']));

    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $_SESSION['error_message'] = "All fields are required.";
        header("Location: contact.php");
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error_message'] = "Invalid email format.";
        header("Location: contact.php");
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $subject, $message);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Your message has been sent successfully!";
    } else {
        $_SESSION['error_message'] = "Error: " . $stmt->error;
    }

    $stmt->close();
    closeDbConnection($conn);

    header("Location: contact.php");
    exit();
}
?> 