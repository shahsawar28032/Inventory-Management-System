<?php
require_once '../../includes/auth_check.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';

$database = new Database();
$conn = $database->getConnection();

$page_title = "Edit Product";
$error = '';
$success = '';

// Get product ID from URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$product_id = $_GET['id'];

// Get product data
$query = "SELECT * FROM products WHERE id = :id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':id', $product_id);
$stmt->execute();

if ($stmt->rowCount() == 0) {
    header("Location: index.php");
    exit;
}

$product = $stmt->fetch(PDO::FETCH_ASSOC);

// Get categories and suppliers for dropdowns
$categories = $conn->query("SELECT * FROM categories ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
$suppliers = $conn->query("SELECT * FROM suppliers ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

if ($_POST) {
    $sku = sanitize($_POST['sku']);
    $name = sanitize($_POST['name']);
    $description = sanitize($_POST['description']);
    $category_id = $_POST['category_id'];
    $supplier_id = $_POST['supplier_id'];
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];
    $cost_price = $_POST['cost_price'];
    $min_stock_level = $_POST['min_stock_level'];
    
    // Validate SKU uniqueness (excluding current product)
    $checkQuery = "SELECT id FROM products WHERE sku = :sku AND id != :id";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bindParam(':sku', $sku);
    $checkStmt->bindParam(':id', $product_id);
    $checkStmt->execute();
    
    if ($checkStmt->rowCount() > 0) {
        $error = "SKU already exists. Please use a different SKU.";
    } else {
        // Handle image upload - keep existing image if no new one is uploaded
        $image = $product['image'];
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            $file_type = $_FILES['image']['type'];
            
            if (in_array($file_type, $allowed_types)) {
                $upload_dir = '../../assets/images/uploads/';
                // Create directory if it doesn't exist
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                // Delete old image if exists
                if (!empty($image) && file_exists($upload_dir . $image)) {
                    unlink($upload_dir . $image);
                }
                
                $image = time() . '_' . basename($_FILES['image']['name']);
                $target_file = $upload_dir . $image;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                    // Image uploaded successfully
                } else {
                    $error = "Sorry, there was an error uploading your file.";
                }
            } else {
                $error = "Only JPG, JPEG, PNG & GIF files are allowed.";
            }
        }
        
        if (empty($error)) {
            // Update product
            $query = "UPDATE products SET sku = :sku, name = :name, description = :description, 
                     category_id = :category_id, supplier_id = :supplier_id, quantity = :quantity, price = :price, 
                     cost_price = :cost_price, image = :image, min_stock_level = :min_stock_level 
                     WHERE id = :id";
            
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':sku', $sku);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':category_id', $category_id);
            $stmt->bindParam(':supplier_id', $supplier_id);
            $stmt->bindParam(':quantity', $quantity);
            $stmt->bindParam(':price', $price);
            $stmt->bindParam(':cost_price', $cost_price);
            $stmt->bindParam(':image', $image);
            $stmt->bindParam(':min_stock_level', $min_stock_level);
            $stmt->bindParam(':id', $product_id);
            
            if ($stmt->execute()) {
                $success = "Product updated successfully!";
                header("Location: index.php?success=1");
                exit;
            } else {
                $error = "Error updating product. Please try again.";
            }
        }
    }
}

require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Edit Product</h1>
        <a href="index.php" class="btn btn-secondary">Back to Products</a>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="sku" class="form-label">SKU *</label>
                            <input type="text" class="form-control" id="sku" name="sku" value="<?php echo htmlspecialchars($product['sku']); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Product Name *</label>
                            <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="category_id" class="form-label">Category</label>
                            <select class="form-select" id="category_id" name="category_id">
                                <option value="">Select Category</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['id']; ?>" <?php echo $product['category_id'] == $category['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($category['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="supplier_id" class="form-label">Supplier</label>
                            <select class="form-select" id="supplier_id" name="supplier_id">
                                <option value="">Select Supplier</option>
                                <?php foreach ($suppliers as $supplier): ?>
                                    <option value="<?php echo $supplier['id']; ?>" <?php echo $product['supplier_id'] == $supplier['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($supplier['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="quantity" class="form-label">Quantity *</label>
                            <input type="number" class="form-control" id="quantity" name="quantity" value="<?php echo $product['quantity']; ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="price" class="form-label">Selling Price *</label>
                            <input type="number" step="0.01" class="form-control" id="price" name="price" value="<?php echo $product['price']; ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="cost_price" class="form-label">Cost Price</label>
                            <input type="number" step="0.01" class="form-control" id="cost_price" name="cost_price" value="<?php echo $product['cost_price']; ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="min_stock_level" class="form-label">Minimum Stock Level</label>
                            <input type="number" class="form-control" id="min_stock_level" name="min_stock_level" value="<?php echo $product['min_stock_level']; ?>">
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="image" class="form-label">Product Image</label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*">
                            <div class="form-text">Leave empty to keep current image</div>
                            <?php if (!empty($product['image'])): ?>
                                <div class="mt-2">
                                    <small>Current image:</small>
                                    <br>
                                    <?php if (file_exists('../../assets/images/uploads/' . $product['image'])): ?>
                                        <img src="../../assets/images/uploads/<?php echo $product['image']; ?>" 
                                             alt="Current Product Image" 
                                             style="max-width: 150px; max-height: 150px; object-fit: cover;" 
                                             class="rounded border mt-1">
                                    <?php else: ?>
                                        <div class="text-muted mt-1">
                                            <i class="fas fa-image"></i> Image file not found
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="4"><?php echo htmlspecialchars($product['description']); ?></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary">Update Product</button>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</main>

<?php require_once '../../includes/footer.php'; ?>