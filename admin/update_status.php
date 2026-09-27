<?php
require_once '../includes/db.php';
require_once 'includes/auth_check.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: orders.php');
    exit;
}

$orderId = (int) ($_POST['order_id'] ?? 0);
$status = trim($_POST['order_status'] ?? '');
$remarks = trim($_POST['remarks'] ?? '');

$validStatuses = ['Order Placed','Preparing','Out for Delivery','Delivered','Cancelled'];
$message = '';

if ($orderId <= 0 || !in_array($status, $validStatuses, true)) {
    $message = 'Invalid order or status.';
} else {
    // A status can be changed any number of times. Every change is retained in
    // delivery_status, including a repeat of the same status with new remarks.
    $conn->begin_transaction();
    try {
        $stmt = $conn->prepare("UPDATE orders SET order_status = ? WHERE id = ?");
        if (!$stmt) throw new RuntimeException($conn->error);
        $stmt->bind_param('si', $status, $orderId);
        if (!$stmt->execute() || $stmt->affected_rows < 0) throw new RuntimeException($stmt->error);

        if ($stmt->affected_rows === 0) {
            $check = $conn->prepare("SELECT id FROM orders WHERE id = ?");
            $check->bind_param('i', $orderId);
            $check->execute();
            if (!$check->get_result()->fetch_assoc()) throw new RuntimeException('Order not found.');
        }

        $stmt2 = $conn->prepare("INSERT INTO delivery_status (order_id, status, remarks) VALUES (?, ?, ?)");
        if (!$stmt2) throw new RuntimeException($conn->error);
        $stmt2->bind_param('iss', $orderId, $status, $remarks);
        if (!$stmt2->execute()) throw new RuntimeException($stmt2->error);

        $conn->commit();
        $message = 'Status updated and added to the history.';
    } catch (Throwable $e) {
        $conn->rollback();
        error_log('Order status update failed: ' . $e->getMessage());
        $message = 'Could not update the status. Please try again.';
    }
}

header('Location: order_details.php?id=' . $orderId . '&msg=' . urlencode($message));
exit;
