<?php
require_once '../../includes/auth_check.php';
require_once '../../config/database.php';

$database = new Database();
$conn = $database->getConnection();

// Simple redirect since orders module is not fully implemented
header("Location: index.php");
exit;
?>