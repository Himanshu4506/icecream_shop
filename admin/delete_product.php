<?php
require_once '../includes/db.php';
require_once 'includes/auth_check.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id > 0) {
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
}

header('Location: products.php?msg=' . urlencode('Product deleted successfully.'));
exit;
