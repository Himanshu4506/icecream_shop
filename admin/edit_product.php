<?php
require_once '../includes/db.php';
require_once 'includes/auth_check.php';
$pageTitle = 'Edit Product';
$active = 'products';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    header('Location: products.php?msg=' . urlencode('Product not found.'));
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = $_POST['price'] ?? '';
    $status = $_POST['status'] ?? 'active';
    $image = $product['image'];

    if ($name === '' || $category === '' || $price === '' || !is_numeric($price) || $price < 0) {
        $error = 'Please fill in all required fields with valid values.';
    } else {
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg','jpeg','png','gif','webp'];
            if (in_array($ext, $allowed)) {
                $newName = uniqid('prod_') . '.' . $ext;
                if (move_uploaded_file($_FILES['image']['tmp_name'], '../uploads/products/' . $newName)) {
                    $image = $newName;
                }
            } else {
                $error = 'Invalid image format. Allowed: jpg, jpeg, png, gif, webp.';
            }
        }

        if (!$error) {
            $stmt = $conn->prepare("UPDATE products SET name=?, category=?, description=?, price=?, image=?, status=? WHERE id=?");
            $stmt->bind_param('sssdssi', $name, $category, $description, $price, $image, $status, $id);
            $stmt->execute();
            header('Location: products.php?msg=' . urlencode('Product updated successfully.'));
            exit;
        }
    }
}

require_once 'includes/admin_header.php';
?>

<div class="stat-card mx-auto" style="max-width:700px;">
  <h5 class="mb-3">Edit Product</h5>
  <?php if ($error): ?><div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

  <form method="POST" action="edit_product.php?id=<?php echo $id; ?>" enctype="multipart/form-data" class="needs-validation" novalidate>
    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Product Name</label>
        <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($product['name']); ?>" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Category</label>
        <input type="text" name="category" class="form-control" value="<?php echo htmlspecialchars($product['category']); ?>" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Price (₹)</label>
        <input type="number" step="0.01" min="0" name="price" class="form-control" value="<?php echo htmlspecialchars($product['price']); ?>" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Status</label>
        <select name="status" class="form-select">
          <option value="active" <?php echo $product['status']==='active'?'selected':''; ?>>Active</option>
          <option value="inactive" <?php echo $product['status']==='inactive'?'selected':''; ?>>Inactive</option>
        </select>
      </div>
      <div class="col-12">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control" rows="3"><?php echo htmlspecialchars($product['description']); ?></textarea>
      </div>
      <div class="col-12">
        <label class="form-label">Replace Image (optional)</label>
        <input type="file" name="image" class="form-control" accept=".jpg,.jpeg,.png,.gif,.webp">
        <small class="text-muted">Current: <?php echo htmlspecialchars($product['image']); ?></small>
      </div>
    </div>
    <div class="mt-4 d-flex gap-2">
      <button type="submit" class="btn btn-hero">Update Product</button>
      <a href="products.php" class="btn btn-hero-outline">Cancel</a>
    </div>
  </form>
</div>

<?php require_once 'includes/admin_footer.php'; ?>
