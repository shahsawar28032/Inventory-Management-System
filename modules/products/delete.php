<?php
require_once '../../includes/auth_check.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';

$database = new Database();
$conn = $database->getConnection();

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$product_id = $_GET['id'];

try {
    // Get product data to delete image file
    $query = "SELECT image FROM products WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $product_id);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Delete image file if exists
        if (!empty($product['image']) && file_exists('../../assets/images/uploads/' . $product['image'])) {
            unlink('../../assets/images/uploads/' . $product['image']);
        }
        
        // Delete product from database
        $deleteQuery = "DELETE FROM products WHERE id = :id";
        $deleteStmt = $conn->prepare($deleteQuery);
        $deleteStmt->bindParam(':id', $product_id);
        
        if ($deleteStmt->execute()) {
            header("Location: index.php?success=Product deleted successfully!");
        } else {
            header("Location: index.php?error=Error deleting product.");
        }
    } else {
        header("Location: index.php?error=Product not found.");
    }
} catch (PDOException $e) {
    header("Location: index.php?error=Database error: " . $e->getMessage());
}
exit;
?>