<?php
require_once 'includes/db.php';
$pageTitle = 'Services';
$basePath = '';

$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$category = isset($_GET['category']) ? trim($_GET['category']) : '';

$sql = "SELECT * FROM products WHERE status='active'";
$params = [];
$types = '';
if ($search !== '') {
    $sql .= " AND name LIKE ?";
    $params[] = '%' . $search . '%';
    $types .= 's';
}
if ($category !== '') {
    $sql .= " AND category = ?";
    $params[] = $category;
    $types .= 's';
}
$sql .= " ORDER BY category, name";

$stmt = $conn->prepare($sql);
if ($params) { $stmt->bind_param($types, ...$params); }
$stmt->execute();
$result = $stmt->get_result();
$products = [];
while ($row = $result->fetch_assoc()) { $products[] = $row; }

$categories = [];
$catRes = $conn->query("SELECT DISTINCT category FROM products WHERE status='active' ORDER BY category");
while ($row = $catRes->fetch_assoc()) { $categories[] = $row['category']; }

require_once 'includes/header.php';
?>

<section class="page-hero">
  <div class="container">
    <h1>Our Services</h1>
    <p class="lead">Flavors, custom orders, catering &amp; delivery — all under one roof.</p>
  </div>
</section>

<!-- SERVICE CARDS -->
<section class="py-5">
  <div class="container">
    <div class="row g-4">
      <div class="col-md-4">
        <div class="service-card text-center">
          <i class="fa-solid fa-palette mb-3 d-block"></i>
          <h5>Custom Ice Cream Orders</h5>
          <p>Mix and match flavors, toppings, and packaging for a truly personalized treat. Perfect for gifts or special requests.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="service-card text-center">
          <i class="fa-solid fa-champagne-glasses mb-3 d-block"></i>
          <h5>Party &amp; Event Catering</h5>
          <p>Birthdays, weddings, corporate events — we bring ice cream carts and bulk tubs to make your event unforgettable.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="service-card text-center">
          <i class="fa-solid fa-truck-fast mb-3 d-block"></i>
          <h5>Home Delivery Service</h5>
          <p>Order online and get fresh ice cream delivered to your door in insulated, spill-proof packaging.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- FLAVOR MENU WITH SEARCH -->
<section class="py-5" style="background:#fff5f8;">
  <div class="container">
    <div class="section-title">
      <h6>Full Menu</h6>
      <h2>Ice Cream Flavors &amp; Categories</h2>
    </div>

    <form class="row g-2 justify-content-center mb-5" method="GET" action="services.php">
      <div class="col-md-5">
        <input type="text" id="flavorSearch" name="q" class="form-control" placeholder="Search flavors..." value="<?php echo htmlspecialchars($search); ?>">
      </div>
      <div class="col-md-3">
        <select name="category" class="form-select" onchange="this.form.submit()">
          <option value="">All Categories</option>
          <?php foreach ($categories as $cat): ?>
            <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo $category === $cat ? 'selected' : ''; ?>><?php echo htmlspecialchars($cat); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-2">
        <button type="submit" class="btn btn-hero w-100">Search</button>
      </div>
    </form>

    <div class="row g-4">
      <?php if (empty($products)): ?>
        <p class="text-center text-muted">No flavors matched your search.</p>
      <?php endif; ?>
      <?php foreach ($products as $p): ?>
        <div class="col-lg-3 col-md-4 col-6 searchable-item" data-name="<?php echo htmlspecialchars($p['name']); ?>">
          <div class="product-card">
            <div class="product-img"><i class="fa-solid fa-ice-cream"></i></div>
            <div class="product-body">
              <div class="product-category"><?php echo htmlspecialchars($p['category']); ?></div>
              <h6 class="mt-1 mb-1"><?php echo htmlspecialchars($p['name']); ?></h6>
              <p class="small text-muted mb-2"><?php echo htmlspecialchars($p['description']); ?></p>
              <div class="product-price">₹<?php echo number_format($p['price'], 2); ?></div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="text-center mt-5">
      <a href="order.php" class="btn btn-hero">Order These Flavors</a>
    </div>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>
