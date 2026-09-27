<?php
require_once 'includes/db.php';
$pageTitle = 'Home';
$basePath = '';

// Featured products (latest active, limit 6)
$products = [];
$res = $conn->query("SELECT * FROM products WHERE status='active' ORDER BY created_at DESC LIMIT 6");
if ($res) { while ($row = $res->fetch_assoc()) { $products[] = $row; } }

// Testimonials
$testimonials = [];
$res2 = $conn->query("SELECT * FROM testimonials ORDER BY created_at DESC LIMIT 4");
if ($res2) { while ($row = $res2->fetch_assoc()) { $testimonials[] = $row; } }

require_once 'includes/header.php';
?>

<!-- HERO -->
<section class="hero">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-lg-6">
        <h6 class="text-uppercase fw-bold" style="color:var(--pink-dark); letter-spacing:2px;">Handcrafted Daily</h6>
        <h1 class="mb-3">Life is short. <br> Eat ice cream <span class="brand-script" style="color:var(--pink-dark);">first!</span></h1>
        <p class="lead">Premium ice cream made from fresh, real ingredients. Order online for pickup, delivery, or your next big celebration.</p>
        <div class="d-flex gap-3 mt-4 flex-wrap">
          <a href="order.php" class="btn btn-hero">Order Now <i class="fa-solid fa-cart-shopping"></i></a>
          <a href="services.php" class="btn btn-hero-outline">Our Flavors</a>
        </div>
      </div>
      <div class="col-lg-6 text-center mt-4 mt-lg-0">
        <div class="hero-emoji">🍦🍨🍧</div>
      </div>
    </div>
  </div>
</section>

<!-- INFO STRIP -->
<div class="info-strip">
  <div class="container d-flex flex-wrap justify-content-center gap-4">
    <div><i class="fa-solid fa-truck-fast"></i> Fast Home Delivery</div>
    <div><i class="fa-solid fa-leaf"></i> Fresh Natural Ingredients</div>
    <div><i class="fa-solid fa-champagne-glasses"></i> Party &amp; Event Catering</div>
    <div><i class="fa-solid fa-star"></i> 15+ Years of Experience</div>
  </div>
</div>

<!-- FEATURED PRODUCTS -->
<section class="py-5 mt-4">
  <div class="container">
    <div class="section-title">
      <h6>Our Menu</h6>
      <h2>Featured Ice Cream Flavors</h2>
    </div>
    <div class="row g-4">
      <?php if (empty($products)): ?>
        <p class="text-center text-muted">No products available right now. Please check back soon!</p>
      <?php endif; ?>
      <?php foreach ($products as $p): ?>
        <div class="col-md-4 col-lg-2 col-6">
          <div class="product-card">
            <div class="product-img"><i class="fa-solid fa-ice-cream"></i></div>
            <div class="product-body">
              <div class="product-category"><?php echo htmlspecialchars($p['category']); ?></div>
              <h6 class="mt-1 mb-1"><?php echo htmlspecialchars($p['name']); ?></h6>
              <div class="product-price">₹<?php echo number_format($p['price'], 2); ?></div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
    <div class="text-center mt-5">
      <a href="services.php" class="btn btn-hero">View Full Menu</a>
    </div>
  </div>
</section>

<!-- TESTIMONIALS -->
<section class="py-5" style="background:#fff5f8;">
  <div class="container">
    <div class="section-title">
      <h6>Testimonials</h6>
      <h2>What Our Customers Say</h2>
    </div>
    <div class="row g-4">
      <?php foreach ($testimonials as $t): ?>
        <div class="col-md-6 col-lg-3">
          <div class="testimonial-card">
            <div class="stars mb-2">
              <?php for ($i=0;$i<$t['rating'];$i++) echo '<i class="fa-solid fa-star"></i> '; ?>
            </div>
            <p class="testimonial-quote">"<?php echo htmlspecialchars($t['message']); ?>"</p>
            <h6 class="mb-0 mt-3">- <?php echo htmlspecialchars($t['customer_name']); ?></h6>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- CONTACT -->
<section class="py-5">
  <div class="container">
    <div class="section-title">
      <h6>Get In Touch</h6>
      <h2>Visit or Contact Us</h2>
    </div>
    <div class="row g-4 text-center">
      <div class="col-md-4">
        <div class="value-card">
          <i class="fa-solid fa-location-dot"></i>
          <h6>Our Shop</h6>
          <p class="mb-0">123 Sweet Street, Flavor Town</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="value-card">
          <i class="fa-solid fa-phone"></i>
          <h6>Call Us</h6>
          <p class="mb-0">+1 (555) 123-4567</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="value-card">
          <i class="fa-solid fa-envelope"></i>
          <h6>Email Us</h6>
          <p class="mb-0">hello@scoophaven.com</p>
        </div>
      </div>
    </div>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>
