<?php
require_once "includes/session.php";
require_once "includes/config.php";

check_login();

if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
    $message_id = intval($_GET["id"]);
    try {
        $sql = "DELETE FROM contact_messages WHERE message_id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":id", $message_id, PDO::PARAM_INT);
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Message deleted successfully.";
        } else {
            $_SESSION['error_message'] = "Error: Could not delete message. Please try again later.";
        }
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Error: Could not delete message. " . $e->getMessage();
    }
} else {
    $_SESSION['error_message'] = "Invalid message ID.";
}

header("Location: messages.php");
exit(); 