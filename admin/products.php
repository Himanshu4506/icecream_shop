<?php
require_once '../includes/db.php';
require_once 'includes/auth_check.php';
$pageTitle = 'Manage Products';
$active = 'products';

$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$sql = "SELECT * FROM products";
$params = []; $types = '';
if ($search !== '') {
    $sql .= " WHERE name LIKE ? OR category LIKE ?";
    $like = '%' . $search . '%';
    $params = [$like, $like];
    $types = 'ss';
}
$sql .= " ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
if ($params) $stmt->bind_param($types, ...$params);
$stmt->execute();
$products = $stmt->get_result();

require_once 'includes/admin_header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
  <form method="GET" action="products.php" class="d-flex gap-2">
    <input type="text" name="q" class="form-control" placeholder="Search products..." value="<?php echo htmlspecialchars($search); ?>">
    <button class="btn btn-outline-secondary"><i class="fa-solid fa-search"></i></button>
  </form>
  <a href="add_product.php" class="btn btn-hero"><i class="fa-solid fa-plus"></i> Add Product</a>
</div>

<?php if (isset($_GET['msg'])): ?>
  <div class="alert alert-success"><?php echo htmlspecialchars($_GET['msg']); ?></div>
<?php endif; ?>

<div class="stat-card">
  <div class="table-responsive">
    <table class="table align-middle">
      <thead><tr><th>#</th><th>Name</th><th>Category</th><th>Price</th><th>Status</th><th>Created</th><th>Actions</th></tr></thead>
      <tbody>
        <?php $i=1; while ($p = $products->fetch_assoc()): ?>
          <tr>
            <td><?php echo $i++; ?></td>
            <td><?php echo htmlspecialchars($p['name']); ?></td>
            <td><?php echo htmlspecialchars($p['category']); ?></td>
            <td>₹<?php echo number_format($p['price'],2); ?></td>
            <td>
              <span class="badge <?php echo $p['status']==='active' ? 'bg-success' : 'bg-secondary'; ?>">
                <?php echo ucfirst($p['status']); ?>
              </span>
            </td>
            <td><?php echo date('M d, Y', strtotime($p['created_at'])); ?></td>
            <td>
              <a href="edit_product.php?id=<?php echo $p['id']; ?>" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-pen"></i></a>
              <a href="delete_product.php?id=<?php echo $p['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this product? This cannot be undone.');"><i class="fa-solid fa-trash"></i></a>
            </td>
          </tr>
        <?php endwhile; ?>
        <?php if ($products->num_rows === 0): ?>
          <tr><td colspan="7" class="text-center text-muted">No products found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once 'includes/admin_footer.php'; ?>
