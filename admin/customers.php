<?php
require_once '../includes/db.php';
require_once 'includes/auth_check.php';
$pageTitle = 'Customers';
$active = 'customers';

$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$sql = "SELECT c.*, COUNT(o.id) AS order_count, COALESCE(SUM(o.total_amount),0) AS total_spent
        FROM customers c LEFT JOIN orders o ON o.customer_id = c.id";
$params = []; $types = '';
if ($search !== '') {
    $sql .= " WHERE c.name LIKE ? OR c.email LIKE ? OR c.phone LIKE ?";
    $like = '%' . $search . '%';
    $params = [$like, $like, $like];
    $types = 'sss';
}
$sql .= " GROUP BY c.id ORDER BY c.created_at DESC";
$stmt = $conn->prepare($sql);
if ($params) $stmt->bind_param($types, ...$params);
$stmt->execute();
$customers = $stmt->get_result();

require_once 'includes/admin_header.php';
?>

<form method="GET" action="customers.php" class="d-flex gap-2 mb-4">
  <input type="text" name="q" class="form-control" style="max-width:320px;" placeholder="Search by name, email, phone..." value="<?php echo htmlspecialchars($search); ?>">
  <button class="btn btn-outline-secondary"><i class="fa-solid fa-search"></i></button>
</form>

<div class="stat-card">
  <div class="table-responsive">
    <table class="table align-middle">
      <thead><tr><th>#</th><th>Name</th><th>Email</th><th>Phone</th><th>Address</th><th>Orders</th><th>Total Spent</th></tr></thead>
      <tbody>
        <?php $i=1; while ($c = $customers->fetch_assoc()): ?>
        <tr>
          <td><?php echo $i++; ?></td>
          <td><?php echo htmlspecialchars($c['name']); ?></td>
          <td><?php echo htmlspecialchars($c['email']); ?></td>
          <td><?php echo htmlspecialchars($c['phone']); ?></td>
          <td><?php echo htmlspecialchars($c['address']); ?></td>
          <td><?php echo (int)$c['order_count']; ?></td>
          <td>₹<?php echo number_format($c['total_spent'],2); ?></td>
        </tr>
        <?php endwhile; ?>
        <?php if ($customers->num_rows === 0): ?>
          <tr><td colspan="7" class="text-center text-muted">No customers found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once 'includes/admin_footer.php'; ?>
