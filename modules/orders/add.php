<?php
require_once '../../includes/auth_check.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';

$database = new Database();
$conn = $database->getConnection();

$page_title = "Create Order";
require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Create Order</h1>
        <a href="index.php" class="btn btn-secondary">Back to Orders</a>
    </div>

    <div class="alert alert-info">
        <h5><i class="fas fa-info-circle"></i> Order Creation</h5>
        <p class="mb-0">The order creation feature is currently under development. This will allow you to create customer orders, add products, calculate totals, and generate order confirmations.</p>
    </div>

    <div class="card">
        <div class="card-body text-center py-5">
            <i class="fas fa-tools fa-4x text-muted mb-3"></i>
            <h4 class="text-muted">Order Creation Coming Soon</h4>
            <p class="text-muted">We're working on implementing order creation functionality.</p>
            <p class="text-muted">For now, please use the Inventory module's Stock Out feature to track product sales.</p>
            <a href="../inventory/stock_out.php" class="btn btn-primary mt-3">
                <i class="fas fa-arrow-up"></i> Go to Stock Out
            </a>
        </div>
    </div>
</main>

<?php require_once '../../includes/footer.php'; ?>