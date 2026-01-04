<?php
require_once '../../includes/auth_check.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';

$database = new Database();
$conn = $database->getConnection();

$page_title = "Edit Category";
$error = '';
$success = '';

// Get category ID from URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$category_id = $_GET['id'];

// Get category data
$query = "SELECT * FROM categories WHERE id = :id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':id', $category_id);
$stmt->execute();

if ($stmt->rowCount() == 0) {
    header("Location: index.php");
    exit;
}

$category = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_POST) {
    $name = sanitize($_POST['name']);
    $description = sanitize($_POST['description']);
    
    // Validate input
    if (empty($name)) {
        $error = "Category name is required.";
    } else {
        // Check if category already exists (excluding current category)
        $check_query = "SELECT id FROM categories WHERE name = :name AND id != :id";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bindParam(':name', $name);
        $check_stmt->bindParam(':id', $category_id);
        $check_stmt->execute();
        
        if ($check_stmt->rowCount() > 0) {
            $error = "Category name already exists.";
        } else {
            // Update category
            $query = "UPDATE categories SET name = :name, description = :description WHERE id = :id";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':id', $category_id);
            
            if ($stmt->execute()) {
                $success = "Category updated successfully!";
                // Refresh category data
                $category['name'] = $name;
                $category['description'] = $description;
            } else {
                $error = "Error updating category. Please try again.";
            }
        }
    }
}

require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Edit Category</h1>
        <a href="index.php" class="btn btn-secondary">Back to Categories</a>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">Category Name *</label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="<?php echo htmlspecialchars($category['name']); ?>" required>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="4"><?php echo htmlspecialchars($category['description']); ?></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary">Update Category</button>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</main>

<?php require_once '../../includes/footer.php'; ?>