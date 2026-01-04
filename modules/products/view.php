<?php
require_once '../../includes/auth_check.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';

$database = new Database();
$conn = $database->getConnection();

$page_title = "View Product";

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$product_id = $_GET['id'];

// Get product data with category and supplier names
$query = "SELECT p.*, c.name as category_name, s.name as supplier_name 
         FROM products p 
         LEFT JOIN categories c ON p.category_id = c.id 
         LEFT JOIN suppliers s ON p.supplier_id = s.id 
         WHERE p.id = :id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':id', $product_id);
$stmt->execute();

if ($stmt->rowCount() == 0) {
    header("Location: index.php");
    exit;
}

$product = $stmt->fetch(PDO::FETCH_ASSOC);

require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">View Product</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="edit.php?id=<?php echo $product_id; ?>" class="btn btn-warning me-2"><i class="fas fa-edit"></i> Edit</a>
            <a href="index.php" class="btn btn-secondary">Back to Products</a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Product Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="30%">SKU:</th>
                                    <td><?php echo htmlspecialchars($product['sku']); ?></td>
                                </tr>
                                <tr>
                                    <th>Name:</th>
                                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                                </tr>
                                <tr>
                                    <th>Category:</th>
                                    <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                                </tr>
                                <tr>
                                    <th>Supplier:</th>
                                    <td><?php echo htmlspecialchars($product['supplier_name']); ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Quantity:</th>
                                    <td>
                                        <span class="stock-level" data-min-stock="<?php echo $product['min_stock_level']; ?>">
                                            <?php echo $product['quantity']; ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Price:</th>
                                    <td><?php echo formatCurrency($product['price']); ?></td>
                                </tr>
                                <tr>
                                    <th>Cost Price:</th>
                                    <td><?php echo formatCurrency($product['cost_price']); ?></td>
                                </tr>
                                <tr>
                                    <th>Min Stock Level:</th>
                                    <td><?php echo $product['min_stock_level']; ?></td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td>
                                        <?php
                                        if ($product['quantity'] == 0) {
                                            echo '<span class="badge bg-danger">Out of Stock</span>';
                                        } elseif ($product['quantity'] <= $product['min_stock_level']) {
                                            echo '<span class="badge bg-warning">Low Stock</span>';
                                        } else {
                                            echo '<span class="badge bg-success">In Stock</span>';
                                        }
                                        ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6>Description:</h6>
                            <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Product Image</h5>
                </div>
                <div class="card-body text-center">
                    <?php if (!empty($product['image']) && file_exists('../../assets/images/uploads/' . $product['image'])): ?>
                        <img src="../../assets/images/uploads/<?php echo $product['image']; ?>" 
                             alt="Product Image" 
                             class="img-fluid rounded" 
                             style="max-height: 300px; object-fit: cover;">
                        <div class="mt-2">
                            <small class="text-muted"><?php echo $product['image']; ?></small>
                        </div>
                    <?php else: ?>
                        <div class="text-muted py-4">
                            <i class="fas fa-image fa-4x mb-3"></i>
                            <p>No image available</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="edit.php?id=<?php echo $product_id; ?>" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Edit Product
                        </a>
                        <a href="../../modules/inventory/stock_in.php?product_id=<?php echo $product_id; ?>" class="btn btn-success">
                            <i class="fas fa-arrow-down"></i> Stock In
                        </a>
                        <a href="../../modules/inventory/stock_out.php?product_id=<?php echo $product_id; ?>" class="btn btn-danger">
                            <i class="fas fa-arrow-up"></i> Stock Out
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once '../../includes/footer.php'; ?>