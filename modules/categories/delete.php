<?php
require_once '../../includes/auth_check.php';
require_once '../../config/database.php';

$database = new Database();
$conn = $database->getConnection();

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$category_id = $_GET['id'];

// Check if category is used in products
$check_query = "SELECT COUNT(*) as count FROM products WHERE category_id = :id";
$check_stmt = $conn->prepare($check_query);
$check_stmt->bindParam(':id', $category_id);
$check_stmt->execute();
$result = $check_stmt->fetch(PDO::FETCH_ASSOC);

if ($result['count'] > 0) {
    header("Location: index.php?error=Cannot delete category. It is being used by products.");
    exit;
}

// Delete category
$delete_query = "DELETE FROM categories WHERE id = :id";
$delete_stmt = $conn->prepare($delete_query);
$delete_stmt->bindParam(':id', $category_id);

if ($delete_stmt->execute()) {
    header("Location: index.php?success=Category deleted successfully!");
} else {
    header("Location: index.php?error=Error deleting category.");
}
exit;
?>