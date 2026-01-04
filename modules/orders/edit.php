<?php
require_once '../../includes/auth_check.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';

$database = new Database();
$conn = $database->getConnection();

$page_title = "Edit Order";
require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Edit Order</h1>
        <a href="index.php" class="btn btn-secondary">Back to Orders</a>
    </div>

    <div class="alert alert-info">
        <h5><i class="fas fa-info-circle"></i> Order Editing</h5>
        <p class="mb-0">The order editing feature is currently under development. This will allow you to modify existing orders, update quantities, change status, and manage order details.</p>
    </div>

    <div class="card">
        <div class="card-body text-center py-5">
            <i class="fas fa-tools fa-4x text-muted mb-3"></i>
            <h4 class="text-muted">Order Editing Coming Soon</h4>
            <p class="text-muted">We're working on implementing order editing functionality.</p>
        </div>
    </div>
</main>

<?php require_once '../../includes/footer.php'; ?>