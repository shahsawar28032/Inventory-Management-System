<?php
require_once '../../includes/auth_check.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';

$database = new Database();
$conn = $database->getConnection();

$page_title = "Reports";
require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Reports</h1>
    </div>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card text-white bg-primary">
                <div class="card-body text-center">
                    <i class="fas fa-boxes fa-3x mb-3"></i>
                    <h5 class="card-title">Inventory Report</h5>
                    <p class="card-text">View complete inventory status</p>
                    <a href="inventory_report.php" class="btn btn-light">View Report</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card text-white bg-success">
                <div class="card-body text-center">
                    <i class="fas fa-chart-line fa-3x mb-3"></i>
                    <h5 class="card-title">Sales Report</h5>
                    <p class="card-text">View sales and revenue data</p>
                    <a href="sales_report.php" class="btn btn-light">View Report</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card text-white bg-warning">
                <div class="card-body text-center">
                    <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                    <h5 class="card-title">Stock Alerts</h5>
                    <p class="card-text">View low stock warnings</p>
                    <a href="stock_alerts.php" class="btn btn-light">View Report</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Quick Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center">
                            <h3><?php echo getTotalProducts($conn); ?></h3>
                            <p class="text-muted">Total Products</p>
                        </div>
                        <div class="col-md-3 text-center">
                            <h3><?php echo getLowStockProducts($conn); ?></h3>
                            <p class="text-muted">Low Stock Items</p>
                        </div>
                        <div class="col-md-3 text-center">
                            <h3><?php echo getStockInCount($conn); ?></h3>
                            <p class="text-muted">Today's Stock In</p>
                        </div>
                        <div class="col-md-3 text-center">
                            <h3><?php echo getStockOutCount($conn); ?></h3>
                            <p class="text-muted">Today's Stock Out</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once '../../includes/footer.php'; ?>