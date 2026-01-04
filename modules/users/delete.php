<?php
require_once '../../includes/auth_check.php';
require_once '../../config/database.php';

// Only admin can access users
if (!isAdmin()) {
    header("Location: ../../index.php");
    exit;
}

$database = new Database();
$conn = $database->getConnection();

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_GET['id'];

// Prevent deleting own account
if ($user_id == $_SESSION['user_id']) {
    header("Location: index.php?error=You cannot delete your own account.");
    exit;
}

// Delete user
$delete_query = "DELETE FROM users WHERE id = :id";
$delete_stmt = $conn->prepare($delete_query);
$delete_stmt->bindParam(':id', $user_id);

if ($delete_stmt->execute()) {
    header("Location: index.php?success=User deleted successfully!");
} else {
    header("Location: index.php?error=Error deleting user.");
}
exit;
?>