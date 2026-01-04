<!-- Sidebar -->
<nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-dark sidebar collapse">
    <div class="position-sticky pt-3">
        <div class="sidebar-header text-center py-4">
            <h4 class="text-white">
                <i class="fas fa-boxes"></i> Inventory
            </h4>
        </div>
        
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" href="/inventory_management/index.php">
                    <i class="fas fa-tachometer-alt me-2"></i>
                    Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'products') !== false ? 'active' : ''; ?>" href="/inventory_management/modules/products/index.php">
                    <i class="fas fa-box me-2"></i>
                    Products
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'categories') !== false ? 'active' : ''; ?>" href="/inventory_management/modules/categories/index.php">
                    <i class="fas fa-tags me-2"></i>
                    Categories
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'suppliers') !== false ? 'active' : ''; ?>" href="/inventory_management/modules/suppliers/index.php">
                    <i class="fas fa-truck me-2"></i>
                    Suppliers
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'inventory') !== false ? 'active' : ''; ?>" href="/inventory_management/modules/inventory/index.php">
                    <i class="fas fa-warehouse me-2"></i>
                    Inventory
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'orders') !== false ? 'active' : ''; ?>" href="/inventory_management/modules/orders/index.php">
                    <i class="fas fa-shopping-cart me-2"></i>
                    Orders
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'reports') !== false ? 'active' : ''; ?>" href="/inventory_management/modules/reports/index.php">
                    <i class="fas fa-chart-bar me-2"></i>
                    Reports
                </a>
            </li>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'Admin'): ?>
            <li class="nav-item">
                <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'users') !== false ? 'active' : ''; ?>" href="/inventory_management/modules/users/index.php">
                    <i class="fas fa-users me-2"></i>
                    Users
                </a>
            </li>
            <?php endif; ?>
            <li class="nav-item">
                <a class="nav-link" href="/inventory_management/logout.php">
                    <i class="fas fa-sign-out-alt me-2"></i>
                    Logout
                </a>
            </li>
        </ul>
    </div>
</nav>