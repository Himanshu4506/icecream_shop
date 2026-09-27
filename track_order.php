<?php
require_once 'includes/db.php';
$pageTitle = 'Track Order';
$basePath = '';

$orderId = isset($_GET['order_id']) ? trim($_GET['order_id']) : '';
$order = null;
$history = [];
$items = [];
$notFound = false;

$statusSteps = ['Order Placed', 'Preparing', 'Out for Delivery', 'Delivered'];

if ($orderId !== '') {
    $stmt = $conn->prepare("SELECT o.*, c.name, c.phone, c.address FROM orders o
                             JOIN customers c ON c.id = o.customer_id
                             WHERE o.order_number = ?");
    $stmt->bind_param('s', $orderId);
    $stmt->execute();
    $order = $stmt->get_result()->fetch_assoc();

    if (!$order) {
        $notFound = true;
    } else {
        $stmt2 = $conn->prepare("SELECT * FROM delivery_status WHERE order_id = ? ORDER BY updated_at ASC, id ASC");
        $stmt2->bind_param('i', $order['id']);
        $stmt2->execute();
        $res2 = $stmt2->get_result();
        while ($row = $res2->fetch_assoc()) { $history[] = $row; }

        $stmt3 = $conn->prepare("SELECT flavor_name, quantity, unit_price, subtotal FROM order_items WHERE order_id = ? ORDER BY id ASC");
        $stmt3->bind_param('i', $order['id']);
        $stmt3->execute();
        $res3 = $stmt3->get_result();
        while ($row = $res3->fetch_assoc()) { $items[] = $row; }
    }
}

require_once 'includes/header.php';
?>

<section class="page-hero">
  <div class="container">
    <h1>Track Your Order</h1>
    <p class="lead">Enter your Order ID to see live delivery status.</p>
  </div>
</section>

<section class="py-5">
  <div class="container">
    <form method="GET" action="track_order.php" class="row g-2 justify-content-center mb-5">
      <div class="col-md-5">
        <input type="text" name="order_id" class="form-control form-control-lg" placeholder="e.g. SH2607131A2B3" value="<?php echo htmlspecialchars($orderId); ?>" required>
      </div>
      <div class="col-md-2">
        <button type="submit" class="btn btn-hero w-100 btn-lg">Track</button>
      </div>
    </form>

    <?php if ($notFound): ?>
      <div class="alert alert-danger text-center mx-auto" style="max-width:500px;">
        No order found with ID "<?php echo htmlspecialchars($orderId); ?>". Please check and try again.
      </div>
    <?php endif; ?>

    <?php if ($order): ?>
      <div class="order-card mx-auto" style="max-width:800px;">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
          <div>
            <h5 class="mb-1">Order #<?php echo htmlspecialchars($order['order_number']); ?></h5>
            <p class="text-muted mb-0">Placed on <?php echo date('M d, Y - h:i A', strtotime($order['created_at'])); ?></p>
          </div>
          <?php
            $badgeClass = [
              'Order Placed' => 'badge-status-placed',
              'Preparing' => 'badge-status-preparing',
              'Out for Delivery' => 'badge-status-out',
              'Delivered' => 'badge-status-delivered',
              'Cancelled' => 'badge-status-cancelled',
            ];
            $cls = $badgeClass[$order['order_status']] ?? 'badge-status-placed';
          ?>
          <span class="badge <?php echo $cls; ?> fs-6 px-3 py-2"><?php echo htmlspecialchars($order['order_status']); ?></span>
        </div>

        <?php if ($order['order_status'] !== 'Cancelled'): ?>
        <!-- Status Tracker -->
        <div class="d-flex mb-5 mt-4">
          <?php
            $currentIndex = array_search($order['order_status'], $statusSteps);
            foreach ($statusSteps as $i => $step):
              $stateClass = '';
              if ($i < $currentIndex) $stateClass = 'done';
              elseif ($i === $currentIndex) $stateClass = 'active';
          ?>
            <div class="tracker-step <?php echo $stateClass; ?>">
              <div class="tracker-line"></div>
              <div class="tracker-circle">
                <?php if ($stateClass === 'done'): ?><i class="fa-solid fa-check"></i>
                <?php else: ?><?php echo $i + 1; ?><?php endif; ?>
              </div>
              <small><?php echo $step; ?></small>
            </div>
          <?php endforeach; ?>
        </div>
        <?php else: ?>
          <div class="alert alert-danger">This order has been cancelled.</div>
        <?php endif; ?>

        <div class="row g-3 mb-4">
          <div class="col-md-6"><strong>Customer:</strong> <?php echo htmlspecialchars($order['name']); ?></div>
          <div class="col-md-6"><strong>Phone:</strong> <?php echo htmlspecialchars($order['phone']); ?></div>
          <div class="col-md-6"><strong>Delivery Type:</strong> <?php echo htmlspecialchars($order['delivery_type']); ?></div>
          <div class="col-md-6"><strong>Total Amount:</strong> ₹<?php echo number_format($order['total_amount'],2); ?></div>
          <?php if ($order['order_status'] !== 'Delivered' && $order['order_status'] !== 'Cancelled'): ?>
          <div class="col-12">
            <strong>Estimated Delivery Time:</strong> <?php echo date('M d, Y - h:i A', strtotime($order['estimated_delivery'])); ?>
          </div>
          <?php endif; ?>
        </div>

        <div class="d-flex justify-content-between align-items-center">
          <h6 class="mb-3">Your Bill</h6>
          <button type="button" class="btn btn-sm btn-outline-secondary mb-3" onclick="window.print()"><i class="fa-solid fa-print"></i> Print Bill</button>
        </div>
        <div class="table-responsive mb-4">
          <table class="table table-sm align-middle">
            <thead><tr><th>Flavor</th><th>Qty</th><th>Price</th><th class="text-end">Subtotal</th></tr></thead>
            <tbody>
              <?php foreach ($items as $it): ?>
              <tr>
                <td><?php echo htmlspecialchars($it['flavor_name']); ?></td>
                <td><?php echo (int)$it['quantity']; ?></td>
                <td>₹<?php echo number_format($it['unit_price'], 2); ?></td>
                <td class="text-end">₹<?php echo number_format($it['subtotal'], 2); ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
            <tfoot><tr><th colspan="3" class="text-end">Total</th><th class="text-end">₹<?php echo number_format($order['total_amount'], 2); ?></th></tr></tfoot>
          </table>
        </div>

        <h6>Status History</h6>
        <ul class="list-group">
          <?php foreach (array_reverse($history) as $h): ?>
            <li class="list-group-item d-flex justify-content-between">
              <span><i class="fa-solid fa-circle-dot me-2" style="color:var(--pink-dark);"></i><?php echo htmlspecialchars($h['status']); ?>
                <?php if ($h['remarks']): ?><br><small class="text-muted ms-4"><?php echo htmlspecialchars($h['remarks']); ?></small><?php endif; ?>
              </span>
              <small class="text-muted"><?php echo date('M d, h:i A', strtotime($h['updated_at'])); ?></small>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>
