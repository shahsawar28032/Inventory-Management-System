<?php
require_once '../../includes/auth_check.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';

$database = new Database();
$conn = $database->getConnection();

$page_title = "Stock Alerts";
require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';

// Get low stock products
$query = "SELECT p.*, c.name as category_name, s.name as supplier_name 
          FROM products p 
          LEFT JOIN categories c ON p.category_id = c.id 
          LEFT JOIN suppliers s ON p.supplier_id = s.id 
          WHERE p.quantity <= p.min_stock_level 
          ORDER BY p.quantity ASC";
$stmt = $conn->prepare($query);
$stmt->execute();
$low_stock_products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get out of stock products
$out_of_stock_query = "SELECT p.*, c.name as category_name, s.name as supplier_name 
                      FROM products p 
                      LEFT JOIN categories c ON p.category_id = c.id 
                      LEFT JOIN suppliers s ON p.supplier_id = s.id 
                      WHERE p.quantity = 0 
                      ORDER BY p.name";
$out_of_stock_stmt = $conn->prepare($out_of_stock_query);
$out_of_stock_stmt->execute();
$out_of_stock_products = $out_of_stock_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Stock Alerts</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <button onclick="window.print()" class="btn btn-secondary me-2">
                <i class="fas fa-print"></i> Print
            </button>
            <a href="index.php" class="btn btn-secondary">Back to Reports</a>
        </div>
    </div>

    <!-- Alert Summary -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-white bg-warning">
                <div class="card-body text-center">
                    <h4><?php echo count($low_stock_products); ?></h4>
                    <p>Low Stock Items</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-danger">
                <div class="card-body text-center">
                    <h4><?php echo count($out_of_stock_products); ?></h4>
                    <p>Out of Stock Items</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-info">
                <div class="card-body text-center">
                    <h4><?php echo date('M j, Y'); ?></h4>
                    <p>Report Date</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Out of Stock Products -->
    <?php if (count($out_of_stock_products) > 0): ?>
    <div class="card mb-4">
        <div class="card-header bg-danger text-white">
            <h5 class="card-title mb-0">
                <i class="fas fa-exclamation-circle"></i> Out of Stock Items (<?php echo count($out_of_stock_products); ?>)
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>SKU</th>
                            <th>Category</th>
                            <th>Supplier</th>
                            <th>Current Stock</th>
                            <th>Min Stock Level</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($out_of_stock_products as $product): ?>
                            <tr class="table-danger">
                                <td><?php echo htmlspecialchars($product['name']); ?></td>
                                <td><?php echo htmlspecialchars($product['sku']); ?></td>
                                <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                                <td><?php echo htmlspecialchars($product['supplier_name']); ?></td>
                                <td><strong>0</strong></td>
                                <td><?php echo $product['min_stock_level']; ?></td>
                                <td>
                                    <a href="../inventory/stock_in.php?product_id=<?php echo $product['id']; ?>" class="btn btn-sm btn-success">
                                        <i class="fas fa-arrow-down"></i> Add Stock
                                    </a>
                                    <a href="../products/edit.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Low Stock Products -->
    <?php if (count($low_stock_products) > 0): ?>
    <div class="card">
        <div class="card-header bg-warning text-dark">
            <h5 class="card-title mb-0">
                <i class="fas fa-exclamation-triangle"></i> Low Stock Items (<?php echo count($low_stock_products); ?>)
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>SKU</th>
                            <th>Category</th>
                            <th>Supplier</th>
                            <th>Current Stock</th>
                            <th>Min Stock Level</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($low_stock_products as $product): ?>
                            <tr class="table-warning">
                                <td><?php echo htmlspecialchars($product['name']); ?></td>
                                <td><?php echo htmlspecialchars($product['sku']); ?></td>
                                <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                                <td><?php echo htmlspecialchars($product['supplier_name']); ?></td>
                                <td><strong><?php echo $product['quantity']; ?></strong></td>
                                <td><?php echo $product['min_stock_level']; ?></td>
                                <td>
                                    <span class="badge bg-warning">Low Stock</span>
                                </td>
                                <td>
                                    <a href="../inventory/stock_in.php?product_id=<?php echo $product['id']; ?>" class="btn btn-sm btn-success">
                                        <i class="fas fa-arrow-down"></i> Add Stock
                                    </a>
                                    <a href="../products/edit.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if (count($out_of_stock_products) == 0 && count($low_stock_products) == 0): ?>
        <div class="alert alert-success">
            <h4 class="alert-heading">Great news!</h4>
            <p class="mb-0">There are currently no stock alerts. All products have sufficient stock levels.</p>
        </div>
    <?php endif; ?>
</main>

<?php require_once '../../includes/footer.php'; ?>