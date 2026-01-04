<?php
require_once '../../includes/auth_check.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';

$database = new Database();
$conn = $database->getConnection();

$page_title = "Stock Out";
$error = '';
$success = '';

// Get products for dropdown
$products_query = "SELECT id, name, sku, quantity FROM products ORDER BY name";
$products_stmt = $conn->prepare($products_query);
$products_stmt->execute();
$products = $products_stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_POST) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    $reference = sanitize($_POST['reference']);
    $notes = sanitize($_POST['notes']);
    
    // Validate input
    if (empty($product_id) || empty($quantity) || $quantity <= 0) {
        $error = "Please select a product and enter a valid quantity.";
    } else {
        // Check if enough stock is available
        $stock_query = "SELECT quantity FROM products WHERE id = :id";
        $stock_stmt = $conn->prepare($stock_query);
        $stock_stmt->bindParam(':id', $product_id);
        $stock_stmt->execute();
        $current_stock = $stock_stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($current_stock['quantity'] < $quantity) {
            $error = "Insufficient stock. Available: " . $current_stock['quantity'];
        } else {
            try {
                $conn->beginTransaction();
                
                // Insert inventory movement
                $movement_query = "INSERT INTO inventory_movements (product_id, movement_type, quantity, reference, notes, created_by) 
                                 VALUES (:product_id, 'OUT', :quantity, :reference, :notes, :created_by)";
                $movement_stmt = $conn->prepare($movement_query);
                $movement_stmt->bindParam(':product_id', $product_id);
                $movement_stmt->bindParam(':quantity', $quantity);
                $movement_stmt->bindParam(':reference', $reference);
                $movement_stmt->bindParam(':notes', $notes);
                $movement_stmt->bindParam(':created_by', $_SESSION['user_id']);
                $movement_stmt->execute();
                
                // Update product quantity
                $update_query = "UPDATE products SET quantity = quantity - :quantity WHERE id = :id";
                $update_stmt = $conn->prepare($update_query);
                $update_stmt->bindParam(':quantity', $quantity);
                $update_stmt->bindParam(':id', $product_id);
                $update_stmt->execute();
                
                $conn->commit();
                $success = "Stock removed successfully!";
                $_POST = array();
                
            } catch (Exception $e) {
                $conn->rollBack();
                $error = "Error processing stock out: " . $e->getMessage();
            }
        }
    }
}

require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Stock Out</h1>
        <a href="index.php" class="btn btn-secondary">Back to Inventory</a>
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
                            <label for="product_id" class="form-label">Product *</label>
                            <select class="form-select" id="product_id" name="product_id" required>
                                <option value="">Select Product</option>
                                <?php foreach ($products as $product): ?>
                                    <option value="<?php echo $product['id']; ?>" 
                                        data-stock="<?php echo $product['quantity']; ?>"
                                        <?php echo (isset($_POST['product_id']) && $_POST['product_id'] == $product['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($product['name']) . ' (' . htmlspecialchars($product['sku']) . ') - Stock: ' . $product['quantity']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="form-text text-muted">Current stock will be updated automatically</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="quantity" class="form-label">Quantity *</label>
                            <input type="number" class="form-control" id="quantity" name="quantity" 
                                   value="<?php echo isset($_POST['quantity']) ? $_POST['quantity'] : ''; ?>" 
                                   min="1" required>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="reference" class="form-label">Reference (e.g., Sales Order)</label>
                            <input type="text" class="form-control" id="reference" name="reference"
                                   value="<?php echo isset($_POST['reference']) ? htmlspecialchars($_POST['reference']) : ''; ?>">
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="notes" class="form-label">Notes</label>
                    <textarea class="form-control" id="notes" name="notes" rows="3"><?php echo isset($_POST['notes']) ? htmlspecialchars($_POST['notes']) : ''; ?></textarea>
                </div>
                
                <button type="submit" class="btn btn-danger">Remove Stock</button>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</main>

<script>
document.getElementById('product_id').addEventListener('change', function() {
    var selectedOption = this.options[this.selectedIndex];
    var maxStock = selectedOption.getAttribute('data-stock');
    document.getElementById('quantity').setAttribute('max', maxStock);
});
</script>

<?php require_once '../../includes/footer.php'; ?>