<?php
require_once '../includes/db.php';
require_once 'includes/auth_check.php';
$pageTitle = 'Dashboard';
$active = 'dashboard';

$totalProducts = $conn->query("SELECT COUNT(*) c FROM products")->fetch_assoc()['c'];
$totalOrders = $conn->query("SELECT COUNT(*) c FROM orders")->fetch_assoc()['c'];
$totalCustomers = $conn->query("SELECT COUNT(*) c FROM customers")->fetch_assoc()['c'];
$totalRevenue = $conn->query("SELECT COALESCE(SUM(total_amount),0) s FROM orders WHERE order_status != 'Cancelled'")->fetch_assoc()['s'];
$pendingOrders = $conn->query("SELECT COUNT(*) c FROM orders WHERE order_status IN ('Order Placed','Preparing','Out for Delivery')")->fetch_assoc()['c'];

$recentOrders = [];
$res = $conn->query("SELECT o.*, c.name FROM orders o JOIN customers c ON c.id=o.customer_id ORDER BY o.created_at DESC LIMIT 8");
while ($row = $res->fetch_assoc()) { $recentOrders[] = $row; }

require_once 'includes/admin_header.php';
?>

<div class="row g-4 mb-4">
  <div class="col-md-3 col-6">
    <div class="stat-card d-flex align-items-center gap-3">
      <i class="fa-solid fa-ice-cream"></i>
      <div><h4 class="mb-0"><?php echo $totalProducts; ?></h4><small class="text-muted">Products</small></div>
    </div>
  </div>
  <div class="col-md-3 col-6">
    <div class="stat-card d-flex align-items-center gap-3">
      <i class="fa-solid fa-receipt"></i>
      <div><h4 class="mb-0"><?php echo $totalOrders; ?></h4><small class="text-muted">Total Orders</small></div>
    </div>
  </div>
  <div class="col-md-3 col-6">
    <div class="stat-card d-flex align-items-center gap-3">
      <i class="fa-solid fa-users"></i>
      <div><h4 class="mb-0"><?php echo $totalCustomers; ?></h4><small class="text-muted">Customers</small></div>
    </div>
  </div>
  <div class="col-md-3 col-6">
    <div class="stat-card d-flex align-items-center gap-3">
      <i class="fa-solid fa-sack-dollar"></i>
      <div><h4 class="mb-0">₹<?php echo number_format($totalRevenue,2); ?></h4><small class="text-muted">Revenue</small></div>
    </div>
  </div>
</div>

<div class="alert alert-warning d-flex align-items-center gap-2">
  <i class="fa-solid fa-triangle-exclamation"></i> You have <strong><?php echo $pendingOrders; ?></strong> order(s) awaiting action.
</div>

<div class="stat-card">
  <h6 class="mb-3">Recent Orders</h6>
  <div class="table-responsive">
    <table class="table align-middle">
      <thead><tr><th>Order ID</th><th>Customer</th><th>Total</th><th>Status</th><th>Date</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($recentOrders as $o):
          $badgeClass = [
            'Order Placed' => 'badge-status-placed','Preparing' => 'badge-status-preparing',
            'Out for Delivery' => 'badge-status-out','Delivered' => 'badge-status-delivered','Cancelled' => 'badge-status-cancelled',
          ];
          $cls = $badgeClass[$o['order_status']] ?? 'badge-status-placed';
        ?>
        <tr>
          <td><?php echo htmlspecialchars($o['order_number']); ?></td>
          <td><?php echo htmlspecialchars($o['name']); ?></td>
          <td>₹<?php echo number_format($o['total_amount'],2); ?></td>
          <td><span class="badge <?php echo $cls; ?>"><?php echo htmlspecialchars($o['order_status']); ?></span></td>
          <td><?php echo date('M d, Y', strtotime($o['created_at'])); ?></td>
          <td><a href="order_details.php?id=<?php echo $o['id']; ?>" class="btn btn-sm btn-outline-secondary">View</a></td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($recentOrders)): ?>
          <tr><td colspan="6" class="text-center text-muted">No orders yet.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once 'includes/admin_footer.php'; ?>
