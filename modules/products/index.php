<?php
require_once '../../includes/auth_check.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';

$database = new Database();
$conn = $database->getConnection();

$page_title = "Products";
require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';

// Handle search and filters
$search = isset($_GET['search']) ? $_GET['search'] : '';
$category_filter = isset($_GET['category']) ? $_GET['category'] : '';
$stock_filter = isset($_GET['stock']) ? $_GET['stock'] : '';

// Build query with filters
$query = "SELECT p.*, c.name as category_name, s.name as supplier_name 
         FROM products p 
         LEFT JOIN categories c ON p.category_id = c.id 
         LEFT JOIN suppliers s ON p.supplier_id = s.id 
         WHERE (p.name LIKE :search OR p.sku LIKE :search)";

$params = [':search' => "%$search%"];

if (!empty($category_filter)) {
    $query .= " AND p.category_id = :category_id";
    $params[':category_id'] = $category_filter;
}

if (!empty($stock_filter)) {
    if ($stock_filter === 'low') {
        $query .= " AND p.quantity <= p.min_stock_level AND p.quantity > 0";
    } elseif ($stock_filter === 'out') {
        $query .= " AND p.quantity = 0";
    } elseif ($stock_filter === 'in') {
        $query .= " AND p.quantity > p.min_stock_level";
    }
}

$query .= " ORDER BY p.created_at DESC";

$stmt = $conn->prepare($query);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get categories for filter dropdown
$categories = $conn->query("SELECT * FROM categories ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Products</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="add.php" class="btn btn-primary"><i class="fas fa-plus"></i> Add Product</a>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="">
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="search" class="form-label">Search Products</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="search" name="search" 
                                       placeholder="Search by name or SKU..." value="<?php echo htmlspecialchars($search); ?>">
                                <button class="btn btn-outline-secondary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="category" class="form-label">Category</label>
                            <select class="form-select" id="category" name="category">
                                <option value="">All Categories</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['id']; ?>" 
                                        <?php echo $category_filter == $category['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($category['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="stock" class="form-label">Stock Status</label>
                            <select class="form-select" id="stock" name="stock">
                                <option value="">All Status</option>
                                <option value="low" <?php echo $stock_filter === 'low' ? 'selected' : ''; ?>>Low Stock</option>
                                <option value="out" <?php echo $stock_filter === 'out' ? 'selected' : ''; ?>>Out of Stock</option>
                                <option value="in" <?php echo $stock_filter === 'in' ? 'selected' : ''; ?>>In Stock</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary me-2">Filter</button>
                            <a href="index.php" class="btn btn-secondary">Reset</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Products Table -->
    <div class="card">
        <div class="card-body">
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success">Product operation completed successfully!</div>
            <?php endif; ?>
            
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger">Error processing product operation.</div>
            <?php endif; ?>

            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>SKU</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($products) > 0): ?>
                            <?php foreach ($products as $product): ?>
                                <?php
                                $status = '';
                                if ($product['quantity'] == 0) {
                                    $status = '<span class="badge bg-danger">Out of Stock</span>';
                                } elseif ($product['quantity'] <= $product['min_stock_level']) {
                                    $status = '<span class="badge bg-warning">Low Stock</span>';
                                } else {
                                    $status = '<span class="badge bg-success">In Stock</span>';
                                }
                                ?>
                                <tr>
                                    <td>
                                        <?php if (!empty($product['image']) && file_exists('../../assets/images/uploads/' . $product['image'])): ?>
                                            <img src="../../assets/images/uploads/<?php echo $product['image']; ?>" 
                                                 alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                                 class="rounded" 
                                                 style="width: 50px; height: 50px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="bg-light rounded d-inline-flex align-items-center justify-content-center" 
                                                 style="width: 50px; height: 50px;">
                                                <i class="fas fa-box text-muted"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($product['sku']); ?></td>
                                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                                    <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                                    <td>
                                        <span class="stock-level" data-min-stock="<?php echo $product['min_stock_level']; ?>">
                                            <?php echo $product['quantity']; ?>
                                        </span>
                                    </td>
                                    <td><?php echo formatCurrency($product['price']); ?></td>
                                    <td><?php echo $status; ?></td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="view.php?id=<?php echo $product['id']; ?>" class="btn btn-info" 
                                               title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="edit.php?id=<?php echo $product['id']; ?>" class="btn btn-warning" 
                                               title="Edit Product">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="delete.php?id=<?php echo $product['id']; ?>" class="btn btn-danger" 
                                               title="Delete Product"
                                               onclick="return confirm('Are you sure you want to delete this product?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-box-open fa-3x mb-3"></i>
                                        <h5>No products found</h5>
                                        <p>Try adjusting your search filters or add a new product.</p>
                                        <a href="add.php" class="btn btn-primary">
                                            <i class="fas fa-plus"></i> Add Your First Product
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Summary -->
            <?php if (count($products) > 0): ?>
                <div class="mt-3">
                    <small class="text-muted">
                        Showing <?php echo count($products); ?> product(s)
                        <?php if (!empty($search)): ?>
                            matching "<?php echo htmlspecialchars($search); ?>"
                        <?php endif; ?>
                    </small>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php require_once '../../includes/footer.php'; ?>