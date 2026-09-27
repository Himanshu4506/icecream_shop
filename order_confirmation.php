<?php
require_once 'includes/db.php';
$pageTitle = 'Order Confirmation';
$basePath = '';

$orderNumber = isset($_GET['order']) ? trim($_GET['order']) : '';
$order = null;
$items = [];

if ($orderNumber !== '') {
    $stmt = $conn->prepare("SELECT o.*, c.name, c.email, c.phone FROM orders o
                             JOIN customers c ON c.id = o.customer_id
                             WHERE o.order_number = ?");
    $stmt->bind_param('s', $orderNumber);
    $stmt->execute();
    $order = $stmt->get_result()->fetch_assoc();

    if ($order) {
        $stmt2 = $conn->prepare("SELECT * FROM order_items WHERE order_id = ?");
        $stmt2->bind_param('i', $order['id']);
        $stmt2->execute();
        $res2 = $stmt2->get_result();
        while ($row = $res2->fetch_assoc()) { $items[] = $row; }
    }
}

require_once 'includes/header.php';
?>

<section class="py-5">
  <div class="container">
    <?php if (!$order): ?>
      <div class="text-center py-5">
        <i class="fa-solid fa-circle-exclamation fa-3x text-danger mb-3"></i>
        <h3>Order Not Found</h3>
        <p class="text-muted">We couldn't find that order. Please check your order ID or place a new order.</p>
        <a href="order.php" class="btn btn-hero mt-3">Place an Order</a>
      </div>
    <?php else: ?>
      <div class="order-card mx-auto" style="max-width:700px;">
        <div class="text-center mb-4">
          <i class="fa-solid fa-circle-check fa-3x" style="color:var(--mint);"></i>
          <h2 class="mt-3">Thank You, <?php echo htmlspecialchars($order['name']); ?>!</h2>
          <p class="text-muted">Your order has been placed successfully.</p>
          <h4 class="mt-3" style="color:var(--pink-dark);">Order ID: <?php echo htmlspecialchars($order['order_number']); ?></h4>
          <p class="small text-muted">Save this Order ID to track your delivery status.</p>
        </div>

        <hr>
        <div class="d-flex justify-content-between align-items-center">
          <h6 class="mb-3">Your Bill — Order Items</h6>
          <button type="button" class="btn btn-sm btn-outline-secondary mb-3" onclick="window.print()"><i class="fa-solid fa-print"></i> Print Bill</button>
        </div>
        <table class="table">
          <thead><tr><th>Flavor</th><th>Qty</th><th>Price</th><th>Subtotal</th></tr></thead>
          <tbody>
            <?php foreach ($items as $it): ?>
              <tr>
                <td><?php echo htmlspecialchars($it['flavor_name']); ?></td>
                <td><?php echo (int)$it['quantity']; ?></td>
                <td>₹<?php echo number_format($it['unit_price'],2); ?></td>
                <td>₹<?php echo number_format($it['subtotal'],2); ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
          <tfoot>
            <tr><th colspan="3" class="text-end">Total</th><th>₹<?php echo number_format($order['total_amount'],2); ?></th></tr>
          </tfoot>
        </table>

        <div class="row g-3 mt-2">
          <div class="col-md-6">
            <strong>Delivery Type:</strong> <?php echo htmlspecialchars($order['delivery_type']); ?>
          </div>
          <div class="col-md-6">
            <strong>Payment Method:</strong> <?php echo htmlspecialchars($order['payment_method']); ?>
          </div>
          <?php if ($order['delivery_address']): ?>
          <div class="col-12">
            <strong>Delivery Address:</strong> <?php echo htmlspecialchars($order['delivery_address']); ?>
          </div>
          <?php endif; ?>
          <div class="col-12">
            <strong>Estimated Ready/Delivery Time:</strong> <?php echo date('M d, Y - h:i A', strtotime($order['estimated_delivery'])); ?>
          </div>
        </div>

        <div class="text-center mt-4">
          <a href="track_order.php?order_id=<?php echo urlencode($order['order_number']); ?>" class="btn btn-hero">Track My Order</a>
          <a href="index.php" class="btn btn-hero-outline">Back to Home</a>
        </div>
      </div>
    <?php endif; ?>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>
