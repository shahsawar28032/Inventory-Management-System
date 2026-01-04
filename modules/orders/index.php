<?php
require_once '../../includes/auth_check.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';

$database = new Database();
$conn = $database->getConnection();

$page_title = "Orders";
require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Orders</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="add.php" class="btn btn-primary"><i class="fas fa-plus"></i> Create Order</a>
        </div>
    </div>

    <div class="alert alert-info">
        <h5><i class="fas fa-info-circle"></i> Orders Module</h5>
        <p class="mb-0">The orders management system is currently under development. This module will allow you to:</p>
        <ul class="mb-0">
            <li>Create customer orders</li>
            <li>Track order status</li>
            <li>Manage order fulfillment</li>
            <li>Generate invoices</li>
        </ul>
    </div>

    <div class="card">
        <div class="card-body text-center py-5">
            <i class="fas fa-tools fa-4x text-muted mb-3"></i>
            <h4 class="text-muted">Orders Module Coming Soon</h4>
            <p class="text-muted">We're working on implementing a comprehensive order management system.</p>
            <p class="text-muted">In the meantime, you can use the Inventory module to track stock movements.</p>
        </div>
    </div>
</main>

<?php require_once '../../includes/footer.php'; ?>