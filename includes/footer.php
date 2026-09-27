<footer class="site-footer">
  <div class="container">
    <div class="row g-4">
      <div class="col-md-4">
        <h5><i class="fa-solid fa-ice-cream"></i> Scoop Haven</h5>
        <p>Handcrafted ice cream made fresh daily with real ingredients and a whole lot of love, since 2010.</p>
        <div class="social-icons">
          <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
          <a href="#"><i class="fa-brands fa-instagram"></i></a>
          <a href="#"><i class="fa-brands fa-twitter"></i></a>
          <a href="#"><i class="fa-brands fa-whatsapp"></i></a>
        </div>
      </div>
      <div class="col-md-4">
        <h5>Quick Links</h5>
        <ul class="footer-links">
          <li><a href="<?php echo isset($basePath) ? $basePath : ''; ?>index.php">Home</a></li>
          <li><a href="<?php echo isset($basePath) ? $basePath : ''; ?>about.php">About Us</a></li>
          <li><a href="<?php echo isset($basePath) ? $basePath : ''; ?>services.php">Services</a></li>
          <li><a href="<?php echo isset($basePath) ? $basePath : ''; ?>order.php">Order Now</a></li>
          <li><a href="<?php echo isset($basePath) ? $basePath : ''; ?>track_order.php">Track Order</a></li>
          <li><a href="<?php echo isset($basePath) ? $basePath : ''; ?>admin/login.php">Admin Login</a></li>
        </ul>
      </div>
      <div class="col-md-4">
        <h5>Contact Us</h5>
        <ul class="footer-links">
          <li><i class="fa-solid fa-location-dot"></i> 123 Sweet Street, Flavor Town</li>
          <li><i class="fa-solid fa-phone"></i> +1 (555) 123-4567</li>
          <li><i class="fa-solid fa-envelope"></i> hello@scoophaven.com</li>
          <li><i class="fa-solid fa-clock"></i> Daily: 10:00 AM - 10:00 PM</li>
        </ul>
      </div>
    </div>
    <hr>
    <p class="text-center mb-0">&copy; <?php echo date('Y'); ?> Scoop Haven Ice Cream Shop. All rights reserved.</p>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo isset($basePath) ? $basePath : ''; ?>js/script.js"></script>
</body>
</html>
