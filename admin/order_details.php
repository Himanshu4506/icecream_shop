<?php
require_once '../includes/db.php';
require_once 'includes/auth_check.php';
$pageTitle = 'Order Details';
$active = 'orders';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$stmt = $conn->prepare("SELECT o.*, c.name, c.email, c.phone FROM orders o JOIN customers c ON c.id=o.customer_id WHERE o.id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    header('Location: orders.php?msg=' . urlencode('Order not found.'));
    exit;
}

$stmt2 = $conn->prepare("SELECT * FROM order_items WHERE order_id = ?");
$stmt2->bind_param('i', $id);
$stmt2->execute();
$items = $stmt2->get_result();

$stmt3 = $conn->prepare("SELECT * FROM delivery_status WHERE order_id = ? ORDER BY updated_at DESC");
$stmt3->bind_param('i', $id);
$stmt3->execute();
$history = $stmt3->get_result();

require_once 'includes/admin_header.php';
?>

<a href="orders.php" class="btn btn-sm btn-outline-secondary mb-3"><i class="fa-solid fa-arrow-left"></i> Back to Orders</a>

<div class="row g-4">
  <div class="col-lg-8">
    <div class="stat-card mb-4">
      <h5 class="mb-3">Order #<?php echo htmlspecialchars($order['order_number']); ?></h5>
      <div class="row g-3">
        <div class="col-md-6"><strong>Customer:</strong> <?php echo htmlspecialchars($order['name']); ?></div>
        <div class="col-md-6"><strong>Email:</strong> <?php echo htmlspecialchars($order['email']); ?></div>
        <div class="col-md-6"><strong>Phone:</strong> <?php echo htmlspecialchars($order['phone']); ?></div>
        <div class="col-md-6"><strong>Payment:</strong> <?php echo htmlspecialchars($order['payment_method']); ?></div>
        <div class="col-md-6"><strong>Delivery Type:</strong> <?php echo htmlspecialchars($order['delivery_type']); ?></div>
        <div class="col-md-6"><strong>Order Date:</strong> <?php echo date('M d, Y - h:i A', strtotime($order['created_at'])); ?></div>
        <?php if ($order['delivery_address']): ?>
        <div class="col-12"><strong>Address:</strong> <?php echo htmlspecialchars($order['delivery_address']); ?></div>
        <?php endif; ?>
        <?php if ($order['notes']): ?>
        <div class="col-12"><strong>Notes:</strong> <?php echo htmlspecialchars($order['notes']); ?></div>
        <?php endif; ?>
      </div>
    </div>

    <div class="stat-card mb-4">
      <h6 class="mb-3">Order Items</h6>
      <table class="table">
        <thead><tr><th>Flavor</th><th>Qty</th><th>Price</th><th>Subtotal</th></tr></thead>
        <tbody>
          <?php while ($it = $items->fetch_assoc()): ?>
          <tr>
            <td><?php echo htmlspecialchars($it['flavor_name']); ?></td>
            <td><?php echo (int)$it['quantity']; ?></td>
            <td>₹<?php echo number_format($it['unit_price'],2); ?></td>
            <td>₹<?php echo number_format($it['subtotal'],2); ?></td>
          </tr>
          <?php endwhile; ?>
        </tbody>
        <tfoot><tr><th colspan="3" class="text-end">Total</th><th>₹<?php echo number_format($order['total_amount'],2); ?></th></tr></tfoot>
      </table>
    </div>

    <div class="stat-card">
      <h6 class="mb-3">Status History</h6>
      <ul class="list-group">
        <?php while ($h = $history->fetch_assoc()): ?>
          <li class="list-group-item d-flex justify-content-between">
            <span><?php echo htmlspecialchars($h['status']); ?>
              <?php if ($h['remarks']): ?><br><small class="text-muted"><?php echo htmlspecialchars($h['remarks']); ?></small><?php endif; ?>
            </span>
            <small class="text-muted"><?php echo date('M d, h:i A', strtotime($h['updated_at'])); ?></small>
          </li>
        <?php endwhile; ?>
      </ul>
    </div>
  </div>

  <div class="col-lg-4">
    <div class="stat-card">
      <h6 class="mb-3">Update Delivery Status</h6>
      <form method="POST" action="update_status.php">
        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
        <div class="mb-3">
          <label class="form-label">Status</label>
          <select name="order_status" class="form-select" required>
            <?php foreach (['Order Placed','Preparing','Out for Delivery','Delivered','Cancelled'] as $s): ?>
              <option value="<?php echo $s; ?>" <?php echo $order['order_status']===$s?'selected':''; ?>><?php echo $s; ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Remarks (optional)</label>
          <input type="text" name="remarks" class="form-control" placeholder="e.g. Driver assigned">
        </div>
        <button type="submit" class="btn btn-hero w-100">Update Status</button>
      </form>
    </div>
  </div>
</div>

<?php require_once 'includes/admin_footer.php'; ?>
