<?php
require_once '../../includes/auth_check.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';

$database = new Database();
$conn = $database->getConnection();

$page_title = "Inventory Report";
require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';

// Get inventory data
$query = "SELECT p.*, c.name as category_name, s.name as supplier_name 
          FROM products p 
          LEFT JOIN categories c ON p.category_id = c.id 
          LEFT JOIN suppliers s ON p.supplier_id = s.id 
          ORDER BY p.name";
$stmt = $conn->prepare($query);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate totals
$total_products = count($products);
$total_value = 0;
$low_stock_count = 0;

foreach ($products as $product) {
    $total_value += $product['quantity'] * $product['cost_price'];
    if ($product['quantity'] <= $product['min_stock_level']) {
        $low_stock_count++;
    }
}
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Inventory Report</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <button onclick="window.print()" class="btn btn-secondary me-2">
                <i class="fas fa-print"></i> Print
            </button>
            <a href="index.php" class="btn btn-secondary">Back to Reports</a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary">
                <div class="card-body text-center">
                    <h4><?php echo $total_products; ?></h4>
                    <p>Total Products</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning">
                <div class="card-body text-center">
                    <h4><?php echo $low_stock_count; ?></h4>
                    <p>Low Stock Items</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success">
                <div class="card-body text-center">
                    <h4><?php echo formatCurrency($total_value); ?></h4>
                    <p>Total Inventory Value</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info">
                <div class="card-body text-center">
                    <h4><?php echo date('M j, Y'); ?></h4>
                    <p>Report Date</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>SKU</th>
                            <th>Product Name</th>
                            <th>Category</th>
                            <th>Supplier</th>
                            <th>Quantity</th>
                            <th>Cost Price</th>
                            <th>Total Value</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                            <?php
                            $product_value = $product['quantity'] * $product['cost_price'];
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
                                <td><?php echo htmlspecialchars($product['sku']); ?></td>
                                <td><?php echo htmlspecialchars($product['name']); ?></td>
                                <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                                <td><?php echo htmlspecialchars($product['supplier_name']); ?></td>
                                <td><?php echo $product['quantity']; ?></td>
                                <td><?php echo formatCurrency($product['cost_price']); ?></td>
                                <td><?php echo formatCurrency($product_value); ?></td>
                                <td><?php echo $status; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr class="table-dark">
                            <td colspan="6" class="text-end"><strong>Total Inventory Value:</strong></td>
                            <td colspan="2"><strong><?php echo formatCurrency($total_value); ?></strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</main>

<?php require_once '../../includes/footer.php'; ?>