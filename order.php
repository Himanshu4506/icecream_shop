<?php
require_once 'includes/db.php';
$pageTitle = 'Order Now';
$basePath = '';

$products = [];
$res = $conn->query("SELECT * FROM products WHERE status='active' ORDER BY category, name");
while ($row = $res->fetch_assoc()) { $products[] = $row; }

$error = isset($_SESSION['order_error']) ? $_SESSION['order_error'] : (isset($_GET['error']) ? $_GET['error'] : '');
if (isset($_SESSION['order_error'])) unset($_SESSION['order_error']);

require_once 'includes/header.php';
?>

<section class="page-hero">
  <div class="container">
    <h1>Place Your Order</h1>
    <p class="lead">Pick your favorite flavors, tell us where to deliver, and we'll take care of the rest.</p>
  </div>
</section>

<section class="py-5">
  <div class="container">

    <?php if ($error): ?>
      <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form action="process_order.php" method="POST" id="orderForm" class="needs-validation" novalidate>
      <div class="row g-4">

        <!-- LEFT: Flavor selection + customer details -->
        <div class="col-lg-8">
          <div class="order-card mb-4">
            <h5 class="mb-3"><i class="fa-solid fa-ice-cream"></i> Step 1: Select Flavors &amp; Quantity</h5>
            <input type="text" id="flavorSearch" class="form-control mb-3" placeholder="Search flavors to add...">
            <div class="row g-3">
              <?php foreach ($products as $p): ?>
                <div class="col-md-4 col-6 searchable-item" data-name="<?php echo htmlspecialchars($p['name']); ?>">
                  <div class="flavor-select-item">
                    <input type="checkbox" onchange="toggleFlavor(<?php echo $p['id']; ?>, '<?php echo htmlspecialchars(addslashes($p['name'])); ?>', <?php echo $p['price']; ?>, this)">
                    <h6 class="mb-1"><?php echo htmlspecialchars($p['name']); ?></h6>
                    <small class="text-muted d-block mb-2"><?php echo htmlspecialchars($p['category']); ?> &middot; ₹<?php echo number_format($p['price'],2); ?></small>
                    <div class="qty-wrapper d-none">
                      <label class="form-label small mb-1">Quantity</label>
                      <input type="number" min="1" value="1" class="form-control form-control-sm qty-input" onchange="changeQty(<?php echo $p['id']; ?>, this)">
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>

          <div class="order-card">
            <h5 class="mb-3"><i class="fa-solid fa-user"></i> Step 2: Your Details</h5>
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Full Name</label>
                <input type="text" name="customer_name" class="form-control" required>
                <div class="invalid-feedback">Please enter your name.</div>
              </div>
              <div class="col-md-6">
                <label class="form-label">Phone Number</label>
                <input type="tel" name="customer_phone" class="form-control" pattern="[0-9+\-\s]{7,15}" required>
                <div class="invalid-feedback">Please enter a valid phone number.</div>
              </div>
              <div class="col-md-6">
                <label class="form-label">Email Address</label>
                <input type="email" name="customer_email" class="form-control" required>
                <div class="invalid-feedback">Please enter a valid email.</div>
              </div>
              <div class="col-md-6">
                <label class="form-label">Payment Method</label>
                <select name="payment_method" class="form-select" required>
                  <option value="Cash on Delivery">Cash on Delivery</option>
                  <option value="Card on Delivery">Card on Delivery</option>
                  <option value="Online Payment">Online Payment (pay at counter)</option>
                </select>
              </div>

              <div class="col-12">
                <label class="form-label d-block">Delivery Type</label>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="delivery_type" value="Home Delivery" checked onclick="toggleDeliveryAddress()">
                  <label class="form-check-label">Home Delivery</label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="delivery_type" value="Pickup" onclick="toggleDeliveryAddress()">
                  <label class="form-check-label">Store Pickup</label>
                </div>
              </div>

              <div class="col-12" id="addressBox">
                <label class="form-label">Delivery Address</label>
                <textarea name="delivery_address" id="delivery_address" class="form-control" rows="2" required></textarea>
                <div class="invalid-feedback">Please enter your delivery address.</div>
              </div>

              <div class="col-12">
                <label class="form-label">Order Notes (optional)</label>
                <textarea name="notes" class="form-control" rows="2" placeholder="Allergies, special instructions, etc."></textarea>
              </div>
            </div>
          </div>
        </div>

        <!-- RIGHT: Order summary -->
        <div class="col-lg-4">
          <div class="order-summary">
            <h5 class="mb-3"><i class="fa-solid fa-receipt"></i> Order Summary</h5>
            <div id="summaryList"><p class="text-muted mb-0">No flavors selected yet.</p></div>
            <hr>
            <div class="d-flex justify-content-between fw-bold fs-5">
              <span>Total</span>
              <span id="summaryTotal">₹0.00</span>
            </div>
            <input type="hidden" name="cart_data" id="cartData" value="{}">
            <button type="submit" id="submitOrderBtn" class="btn btn-hero w-100 mt-4" disabled>Confirm Order <i class="fa-solid fa-check"></i></button>
            <p class="small text-muted mt-3 mb-0">By placing an order you agree to our terms and confirm your details are accurate.</p>
          </div>
        </div>

      </div>
    </form>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>
<script>initOrderForm();</script>
