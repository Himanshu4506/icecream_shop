<?php
require_once 'includes/db.php';
$pageTitle = 'Your Order Bill';
$basePath = '';

$orderNumber = trim($_GET['order'] ?? '');
$order = null;
$items = [];

if ($orderNumber !== '') {
    $stmt = $conn->prepare("SELECT o.*, c.name, c.email, c.phone FROM orders o JOIN customers c ON c.id = o.customer_id WHERE o.order_number = ?");
    $stmt->bind_param('s', $orderNumber);
    $stmt->execute();
    $order = $stmt->get_result()->fetch_assoc();

    if ($order) {
        $stmt2 = $conn->prepare("SELECT flavor_name, quantity, unit_price, subtotal FROM order_items WHERE order_id = ? ORDER BY id ASC");
        $stmt2->bind_param('i', $order['id']);
        $stmt2->execute();
        $result = $stmt2->get_result();
        while ($item = $result->fetch_assoc()) {
            $items[] = $item;
        }
    }
}

require_once 'includes/header.php';
?>

<section class="py-5">
  <div class="container">
    <?php if (!$order): ?>
      <div class="order-card mx-auto text-center" style="max-width:600px;">
        <i class="fa-solid fa-file-invoice fa-3x mb-3" style="color:var(--pink-dark);"></i>
        <h3>Bill Not Found</h3>
        <p class="text-muted">Please use the bill link from your order confirmation, or enter a valid Order ID on the tracking page.</p>
        <a href="track_order.php" class="btn btn-hero">Track My Order</a>
      </div>
    <?php else: ?>
      <div class="order-card mx-auto" style="max-width:750px;">
        <div class="d-flex flex-wrap justify-content-between align-items-start border-bottom pb-3 mb-3">
          <div>
            <h2 class="mb-1">Scoop Haven</h2>
            <p class="text-muted mb-0">Order Bill</p>
          </div>
          <div class="text-md-end">
            <strong>Order ID:</strong> <?php echo htmlspecialchars($order['order_number']); ?><br>
            <small class="text-muted"><?php echo date('M d, Y - h:i A', strtotime($order['created_at'])); ?></small>
          </div>
        </div>

        <div class="row mb-4">
          <div class="col-md-6"><strong>Customer:</strong> <?php echo htmlspecialchars($order['name']); ?></div>
          <div class="col-md-6"><strong>Phone:</strong> <?php echo htmlspecialchars($order['phone']); ?></div>
          <div class="col-md-6"><strong>Delivery:</strong> <?php echo htmlspecialchars($order['delivery_type']); ?></div>
          <div class="col-md-6"><strong>Payment:</strong> <?php echo htmlspecialchars($order['payment_method']); ?></div>
        </div>

        <div class="table-responsive">
          <table class="table">
            <thead><tr><th>Item</th><th>Qty</th><th>Rate</th><th class="text-end">Amount</th></tr></thead>
            <tbody>
              <?php foreach ($items as $item): ?>
              <tr>
                <td><?php echo htmlspecialchars($item['flavor_name']); ?></td>
                <td><?php echo (int) $item['quantity']; ?></td>
                <td>₹<?php echo number_format($item['unit_price'], 2); ?></td>
                <td class="text-end">₹<?php echo number_format($item['subtotal'], 2); ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
            <tfoot><tr><th colspan="3" class="text-end">Grand Total</th><th class="text-end">₹<?php echo number_format($order['total_amount'], 2); ?></th></tr></tfoot>
          </table>
        </div>

        <div class="d-flex justify-content-center gap-2 mt-4">
          <button type="button" class="btn btn-hero" onclick="window.print()"><i class="fa-solid fa-print"></i> Print Bill</button>
          <a href="track_order.php?order_id=<?php echo urlencode($order['order_number']); ?>" class="btn btn-hero-outline">Track Order</a>
        </div>
      </div>
    <?php endif; ?>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>
