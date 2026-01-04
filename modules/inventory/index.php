<?php
require_once '../../includes/auth_check.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';

$database = new Database();
$conn = $database->getConnection();

$page_title = "Inventory Movements";
require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';

// Get inventory movements with product names
$query = "SELECT im.*, p.name as product_name, p.sku as product_sku 
          FROM inventory_movements im 
          JOIN products p ON im.product_id = p.id 
          ORDER BY im.created_at DESC";
$stmt = $conn->prepare($query);
$stmt->execute();
$movements = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Inventory Movements</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <a href="stock_in.php" class="btn btn-success"><i class="fas fa-arrow-down"></i> Stock In</a>
                <a href="stock_out.php" class="btn btn-danger"><i class="fas fa-arrow-up"></i> Stock Out</a>
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
                            <th>Type</th>
                            <th>Quantity</th>
                            <th>Reference</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($movements) > 0): ?>
                            <?php foreach ($movements as $movement): ?>
                                <tr>
                                    <td><?php echo formatDate($movement['created_at']); ?></td>
                                    <td><?php echo htmlspecialchars($movement['product_name']); ?></td>
                                    <td><?php echo htmlspecialchars($movement['product_sku']); ?></td>
                                    <td>
                                        <?php if ($movement['movement_type'] == 'IN'): ?>
                                            <span class="badge bg-success">IN</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">OUT</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo $movement['quantity']; ?></td>
                                    <td><?php echo htmlspecialchars($movement['reference']); ?></td>
                                    <td><?php echo htmlspecialchars($movement['notes']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center">No inventory movements found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<?php require_once '../../includes/footer.php'; ?>