<?php
require_once 'includes/auth_check.php';
require_once 'config/database.php';
require_once 'includes/functions.php';

$database = new Database();
$conn = $database->getConnection();

$page_title = "Dashboard";
require_once 'includes/header.php';
require_once 'includes/sidebar.php';
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Dashboard</h1>
    </div>

    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title">Total Products</h5>
                            <h2 class="mb-0"><?php echo getTotalProducts($conn); ?></h2>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-box fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title">Stock In (Today)</h5>
                            <h2 class="mb-0"><?php echo getStockInCount($conn); ?></h2>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-arrow-down fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card text-white bg-danger">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title">Stock Out (Today)</h5>
                            <h2 class="mb-0"><?php echo getStockOutCount($conn); ?></h2>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-arrow-up fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title">Low Stock</h5>
                            <h2 class="mb-0"><?php echo getLowStockProducts($conn); ?></h2>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Products & Low Stock Alerts -->
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Recent Products</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $query = "SELECT p.*, c.name as category_name 
                                         FROM products p 
                                         LEFT JOIN categories c ON p.category_id = c.id 
                                         ORDER BY p.created_at DESC 
                                         LIMIT 5";
                                $stmt = $conn->prepare($query);
                                $stmt->execute();
                                
                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['category_name']) . "</td>";
                                    echo "<td>" . $row['quantity'] . "</td>";
                                    echo "<td>" . formatCurrency($row['price']) . "</td>";
                                    echo "<td>
                                            <a href='modules/products/view.php?id=" . $row['id'] . "' class='btn btn-sm btn-info'><i class='fas fa-eye'></i></a>
                                            <a href='modules/products/edit.php?id=" . $row['id'] . "' class='btn btn-sm btn-warning'><i class='fas fa-edit'></i></a>
                                          </td>";
                                    echo "</tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-warning">
                    <h5 class="card-title mb-0 text-white">Low Stock Alerts</h5>
                </div>
                <div class="card-body">
                    <?php
                    $query = "SELECT name, quantity, min_stock_level 
                             FROM products 
                             WHERE quantity <= min_stock_level 
                             ORDER BY quantity ASC 
                             LIMIT 5";
                    $stmt = $conn->prepare($query);
                    $stmt->execute();
                    
                    if ($stmt->rowCount() > 0) {
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            echo "<div class='alert alert-warning py-2 mb-2'>
                                    <strong>" . htmlspecialchars($row['name']) . "</strong><br>
                                    <small>Stock: " . $row['quantity'] . " (Min: " . $row['min_stock_level'] . ")</small>
                                  </div>";
                        }
                    } else {
                        echo "<p class='text-muted'>No low stock alerts.</p>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>