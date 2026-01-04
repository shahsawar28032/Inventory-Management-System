<?php
require_once '../../includes/auth_check.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';

$database = new Database();
$conn = $database->getConnection();

$page_title = "Sales Report";
require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';

// Get date range from URL or use default (last 30 days)
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d', strtotime('-30 days'));
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

// Get stock out movements (sales)
$query = "SELECT im.*, p.name as product_name, p.sku, p.price 
          FROM inventory_movements im 
          JOIN products p ON im.product_id = p.id 
          WHERE im.movement_type = 'OUT' 
          AND DATE(im.created_at) BETWEEN :start_date AND :end_date 
          ORDER BY im.created_at DESC";
$stmt = $conn->prepare($query);
$stmt->bindParam(':start_date', $start_date);
$stmt->bindParam(':end_date', $end_date);
$stmt->execute();
$sales = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate totals
$total_quantity = 0;
$total_revenue = 0;

foreach ($sales as $sale) {
    $total_quantity += $sale['quantity'];
    $total_revenue += $sale['quantity'] * $sale['price'];
}
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Sales Report</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <button onclick="window.print()" class="btn btn-secondary me-2">
                <i class="fas fa-print"></i> Print
            </button>
            <a href="index.php" class="btn btn-secondary">Back to Reports</a>
        </div>
    </div>

    <!-- Date Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="" class="row">
                <div class="col-md-4">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo $start_date; ?>">
                </div>
                <div class="col-md-4">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo $end_date; ?>">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">Filter</button>
                    <a href="sales_report.php" class="btn btn-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-white bg-primary">
                <div class="card-body text-center">
                    <h4><?php echo count($sales); ?></h4>
                    <p>Total Sales Transactions</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-success">
                <div class="card-body text-center">
                    <h4><?php echo $total_quantity; ?></h4>
                    <p>Total Items Sold</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-info">
                <div class="card-body text-center">
                    <h4><?php echo formatCurrency($total_revenue); ?></h4>
                    <p>Total Revenue</p>
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
                            <th>Date</th>
                            <th>Product</th>
                            <th>SKU</th>
                            <th>Quantity</th>
                            <th>Unit Price</th>
                            <th>Total Amount</th>
                            <th>Reference</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($sales) > 0): ?>
                            <?php foreach ($sales as $sale): ?>
                                <?php $sale_amount = $sale['quantity'] * $sale['price']; ?>
                                <tr>
                                    <td><?php echo formatDate($sale['created_at']); ?></td>
                                    <td><?php echo htmlspecialchars($sale['product_name']); ?></td>
                                    <td><?php echo htmlspecialchars($sale['sku']); ?></td>
                                    <td><?php echo $sale['quantity']; ?></td>
                                    <td><?php echo formatCurrency($sale['price']); ?></td>
                                    <td><?php echo formatCurrency($sale_amount); ?></td>
                                    <td><?php echo htmlspecialchars($sale['reference']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center">No sales data found for the selected period.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                    <tfoot>
                        <tr class="table-dark">
                            <td colspan="3" class="text-end"><strong>Totals:</strong></td>
                            <td><strong><?php echo $total_quantity; ?></strong></td>
                            <td></td>
                            <td><strong><?php echo formatCurrency($total_revenue); ?></strong></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</main>

<?php require_once '../../includes/footer.php'; ?>