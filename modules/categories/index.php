<?php
require_once '../../includes/auth_check.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';

$database = new Database();
$conn = $database->getConnection();

$page_title = "Categories";
require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';

// Handle delete
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    
    // Check if category is used in products
    $check_query = "SELECT COUNT(*) as count FROM products WHERE category_id = :id";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bindParam(':id', $delete_id);
    $check_stmt->execute();
    $result = $check_stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result['count'] > 0) {
        $error = "Cannot delete category. It is being used by products.";
    } else {
        $delete_query = "DELETE FROM categories WHERE id = :id";
        $delete_stmt = $conn->prepare($delete_query);
        $delete_stmt->bindParam(':id', $delete_id);
        
        if ($delete_stmt->execute()) {
            $success = "Category deleted successfully!";
        } else {
            $error = "Error deleting category.";
        }
    }
}

// Get all categories
$query = "SELECT * FROM categories ORDER BY name";
$stmt = $conn->prepare($query);
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Categories</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="add.php" class="btn btn-primary"><i class="fas fa-plus"></i> Add Category</a>
        </div>
    </div>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($categories) > 0): ?>
                            <?php foreach ($categories as $category): ?>
                                <tr>
                                    <td><?php echo $category['id']; ?></td>
                                    <td><?php echo htmlspecialchars($category['name']); ?></td>
                                    <td><?php echo htmlspecialchars($category['description']); ?></td>
                                    <td><?php echo formatDate($category['created_at']); ?></td>
                                    <td>
                                        <a href="edit.php?id=<?php echo $category['id']; ?>" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <a href="index.php?delete_id=<?php echo $category['id']; ?>" class="btn btn-sm btn-danger" 
                                           onclick="return confirm('Are you sure you want to delete this category?')">
                                            <i class="fas fa-trash"></i> Delete
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center">No categories found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<?php require_once '../../includes/footer.php'; ?>