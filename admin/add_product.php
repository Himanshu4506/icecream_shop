<?php
require_once '../includes/db.php';
require_once 'includes/auth_check.php';
$pageTitle = 'Add Product';
$active = 'products';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = $_POST['price'] ?? '';
    $status = $_POST['status'] ?? 'active';
    $image = 'default.jpg';

    if ($name === '' || $category === '' || $price === '' || !is_numeric($price) || $price < 0) {
        $error = 'Please fill in all required fields with valid values.';
    } else {
        // Handle optional image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg','jpeg','png','gif','webp'];
            if (in_array($ext, $allowed)) {
                $newName = uniqid('prod_') . '.' . $ext;
                $dest = '../uploads/products/' . $newName;
                if (move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
                    $image = $newName;
                }
            } else {
                $error = 'Invalid image format. Allowed: jpg, jpeg, png, gif, webp.';
            }
        }

        if (!$error) {
            $stmt = $conn->prepare("INSERT INTO products (name, category, description, price, image, status) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param('sssdss', $name, $category, $description, $price, $image, $status);
            $stmt->execute();
            header('Location: products.php?msg=' . urlencode('Product added successfully.'));
            exit;
        }
    }
}

require_once 'includes/admin_header.php';
?>

<div class="stat-card mx-auto" style="max-width:700px;">
  <h5 class="mb-3">Add New Product</h5>
  <?php if ($error): ?><div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

  <form method="POST" action="add_product.php" enctype="multipart/form-data" class="needs-validation" novalidate>
    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Product Name</label>
        <input type="text" name="name" class="form-control" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Category</label>
        <input type="text" name="category" class="form-control" placeholder="e.g. Classic, Fruity, Premium" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Price (₹)</label>
        <input type="number" step="0.01" min="0" name="price" class="form-control" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Status</label>
        <select name="status" class="form-select">
          <option value="active">Active</option>
          <option value="inactive">Inactive</option>
        </select>
      </div>
      <div class="col-12">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control" rows="3"></textarea>
      </div>
      <div class="col-12">
        <label class="form-label">Product Image (optional)</label>
        <input type="file" name="image" class="form-control" accept=".jpg,.jpeg,.png,.gif,.webp">
      </div>
    </div>
    <div class="mt-4 d-flex gap-2">
      <button type="submit" class="btn btn-hero">Save Product</button>
      <a href="products.php" class="btn btn-hero-outline">Cancel</a>
    </div>
  </form>
</div>

<?php require_once 'includes/admin_footer.php'; ?>
