<?php
require_once '../includes/db.php';
require_once 'includes/auth_check.php';
$pageTitle = 'Manage Orders';
$active = 'orders';

$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$statusFilter = isset($_GET['status']) ? trim($_GET['status']) : '';

$sql = "SELECT o.*, c.name, c.phone FROM orders o JOIN customers c ON c.id = o.customer_id WHERE 1=1";
$params = []; $types = '';
if ($search !== '') {
    $sql .= " AND (o.order_number LIKE ? OR c.name LIKE ? OR c.phone LIKE ?)";
    $like = '%' . $search . '%';
    $params = [$like, $like, $like];
    $types = 'sss';
}
if ($statusFilter !== '') {
    $sql .= " AND o.order_status = ?";
    $params[] = $statusFilter;
    $types .= 's';
}
$sql .= " ORDER BY o.created_at DESC";
$stmt = $conn->prepare($sql);
if ($params) $stmt->bind_param($types, ...$params);
$stmt->execute();
$orders = $stmt->get_result();

require_once 'includes/admin_header.php';
?>

<?php if (isset($_GET['msg'])): ?>
  <div class="alert alert-success"><?php echo htmlspecialchars($_GET['msg']); ?></div>
<?php endif; ?>

<form method="GET" action="orders.php" class="d-flex gap-2 mb-4 flex-wrap">
  <input type="text" name="q" class="form-control" style="max-width:280px;" placeholder="Search by Order ID, name, phone..." value="<?php echo htmlspecialchars($search); ?>">
  <select name="status" class="form-select" style="max-width:220px;">
    <option value="">All Statuses</option>
    <?php foreach (['Order Placed','Preparing','Out for Delivery','Delivered','Cancelled'] as $s): ?>
      <option value="<?php echo $s; ?>" <?php echo $statusFilter===$s?'selected':''; ?>><?php echo $s; ?></option>
    <?php endforeach; ?>
  </select>
  <button class="btn btn-outline-secondary"><i class="fa-solid fa-filter"></i> Filter</button>
</form>

<div class="stat-card">
  <div class="table-responsive">
    <table class="table align-middle">
      <thead><tr><th>Order ID</th><th>Customer</th><th>Phone</th><th>Total</th><th>Status</th><th>Date</th><th>Actions</th></tr></thead>
      <tbody>
        <?php
        $badgeClass = ['Order Placed'=>'badge-status-placed','Preparing'=>'badge-status-preparing','Out for Delivery'=>'badge-status-out','Delivered'=>'badge-status-delivered','Cancelled'=>'badge-status-cancelled'];
        while ($o = $orders->fetch_assoc()): $cls = $badgeClass[$o['order_status']] ?? 'badge-status-placed'; ?>
        <tr>
          <td><?php echo htmlspecialchars($o['order_number']); ?></td>
          <td><?php echo htmlspecialchars($o['name']); ?></td>
          <td><?php echo htmlspecialchars($o['phone']); ?></td>
          <td>₹<?php echo number_format($o['total_amount'],2); ?></td>
          <td><span class="badge <?php echo $cls; ?>"><?php echo htmlspecialchars($o['order_status']); ?></span></td>
          <td><?php echo date('M d, Y', strtotime($o['created_at'])); ?></td>
          <td><a href="order_details.php?id=<?php echo $o['id']; ?>" class="btn btn-sm btn-outline-primary">Manage</a></td>
        </tr>
        <?php endwhile; ?>
        <?php if ($orders->num_rows === 0): ?>
          <tr><td colspan="7" class="text-center text-muted">No orders found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once 'includes/admin_footer.php'; ?>
