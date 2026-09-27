<?php
require_once 'includes/db.php';
if (session_status() === PHP_SESSION_NONE) session_start();

function backToOrderWithError($msg) {
    $_SESSION['order_error'] = $msg;
    header('Location: order.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: order.php');
    exit;
}

// ---- Collect & validate customer details ----
$name  = trim($_POST['customer_name'] ?? '');
$phone = trim($_POST['customer_phone'] ?? '');
$email = trim($_POST['customer_email'] ?? '');
$payment_method = trim($_POST['payment_method'] ?? 'Cash on Delivery');
$delivery_type  = trim($_POST['delivery_type'] ?? 'Home Delivery');
$delivery_address = trim($_POST['delivery_address'] ?? '');
$notes = trim($_POST['notes'] ?? '');
$cartJson = $_POST['cart_data'] ?? '{}';

if ($name === '' || $phone === '' || $email === '') {
    backToOrderWithError('Please fill in all required customer details.');
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    backToOrderWithError('Please enter a valid email address.');
}
if ($delivery_type === 'Home Delivery' && $delivery_address === '') {
    backToOrderWithError('Please enter a delivery address for home delivery.');
}

$cart = json_decode($cartJson, true);
if (!is_array($cart) || count($cart) === 0) {
    backToOrderWithError('Please select at least one ice cream flavor.');
}

// ---- Validate cart items against DB (prevent tampered prices) ----
$items = [];
$total = 0;
foreach ($cart as $productId => $item) {
    $productId = (int) $productId;
    $qty = isset($item['qty']) ? (int) $item['qty'] : 0;
    if ($qty < 1) continue;

    $stmt = $conn->prepare("SELECT id, name, price FROM products WHERE id = ? AND status = 'active'");
    $stmt->bind_param('i', $productId);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    if (!$product) continue;

    $subtotal = $product['price'] * $qty;
    $total += $subtotal;
    $items[] = [
        'product_id' => $product['id'],
        'name' => $product['name'],
        'qty' => $qty,
        'price' => $product['price'],
        'subtotal' => $subtotal
    ];
}

if (empty($items)) {
    backToOrderWithError('Your selected flavors are no longer available. Please try again.');
}

// Keep all related inserts together: an incomplete order must never be saved.
$conn->begin_transaction();

try {
    // ---- Insert customer ----
    $stmt = $conn->prepare("INSERT INTO customers (name, email, phone, address) VALUES (?, ?, ?, ?)");
    if (!$stmt) throw new RuntimeException($conn->error);
    $stmt->bind_param('ssss', $name, $email, $phone, $delivery_address);
    if (!$stmt->execute()) throw new RuntimeException($stmt->error);
    $customerId = $conn->insert_id;

    // ---- Generate unique order number ----
    $orderNumber = 'SH' . date('ymd') . strtoupper(substr(uniqid(), -5));

    // ---- Estimated delivery: 45-60 minutes from now for delivery, 20 min for pickup ----
    $minutes = $delivery_type === 'Home Delivery' ? 50 : 20;
    $estimated = date('Y-m-d H:i:s', strtotime("+{$minutes} minutes"));

    // ---- Insert order ----
    $stmt = $conn->prepare("INSERT INTO orders (order_number, customer_id, total_amount, delivery_type, delivery_address, payment_method, notes, order_status, estimated_delivery)
                             VALUES (?, ?, ?, ?, ?, ?, ?, 'Order Placed', ?)");
    if (!$stmt) throw new RuntimeException($conn->error);
    // Eight values are bound here: order number, customer, total, and five text fields.
    $stmt->bind_param('sidsssss', $orderNumber, $customerId, $total, $delivery_type, $delivery_address, $payment_method, $notes, $estimated);
    if (!$stmt->execute()) throw new RuntimeException($stmt->error);
    $orderId = $conn->insert_id;

    // ---- Insert order items ----
    $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, flavor_name, quantity, unit_price, subtotal) VALUES (?, ?, ?, ?, ?, ?)");
    if (!$stmt) throw new RuntimeException($conn->error);
    foreach ($items as $it) {
        $stmt->bind_param('iisidd', $orderId, $it['product_id'], $it['name'], $it['qty'], $it['price'], $it['subtotal']);
        if (!$stmt->execute()) throw new RuntimeException($stmt->error);
    }

    // ---- Insert initial delivery status log ----
    $stmt = $conn->prepare("INSERT INTO delivery_status (order_id, status, remarks) VALUES (?, 'Order Placed', 'Order received successfully.')");
    if (!$stmt) throw new RuntimeException($conn->error);
    $stmt->bind_param('i', $orderId);
    if (!$stmt->execute()) throw new RuntimeException($stmt->error);

    $conn->commit();
} catch (Throwable $e) {
    $conn->rollback();
    error_log('Order processing failed: ' . $e->getMessage());
    backToOrderWithError('We could not place your order. Please try again.');
}

header('Location: order_confirmation.php?order=' . urlencode($orderNumber));
exit;
