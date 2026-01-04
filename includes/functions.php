<?php
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'Admin';
}

function redirect($url) {
    header("Location: " . $url);
    exit;
}

function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function formatDate($date) {
    return date('M j, Y', strtotime($date));
}

function formatCurrency($amount) {
    return '$' . number_format($amount, 2);
}

function getLowStockProducts($conn) {
    $query = "SELECT COUNT(*) as count FROM products WHERE quantity <= min_stock_level";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['count'];
}

function getTotalProducts($conn) {
    $query = "SELECT COUNT(*) as count FROM products";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['count'];
}

function getStockInCount($conn) {
    $query = "SELECT SUM(quantity) as total FROM inventory_movements WHERE movement_type = 'IN' AND DATE(created_at) = CURDATE()";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['total'] ?: 0;
}

function getStockOutCount($conn) {
    $query = "SELECT SUM(quantity) as total FROM inventory_movements WHERE movement_type = 'OUT' AND DATE(created_at) = CURDATE()";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['total'] ?: 0;
}
?>