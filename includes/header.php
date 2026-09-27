<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) . ' - Scoop Haven' : 'Scoop Haven Ice Cream Shop'; ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Pacifico&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?php echo isset($basePath) ? $basePath : ''; ?>css/style.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light sticky-top main-navbar">
  <div class="container">
    <a class="navbar-brand" href="<?php echo isset($basePath) ? $basePath : ''; ?>index.php">
      <i class="fa-solid fa-ice-cream"></i> Scoop Haven
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="mainNav">
      <ul class="navbar-nav ms-auto align-items-lg-center">
        <li class="nav-item"><a class="nav-link" href="<?php echo isset($basePath) ? $basePath : ''; ?>index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="<?php echo isset($basePath) ? $basePath : ''; ?>about.php">About Us</a></li>
        <li class="nav-item"><a class="nav-link" href="<?php echo isset($basePath) ? $basePath : ''; ?>services.php">Services</a></li>
        <li class="nav-item"><a class="nav-link" href="<?php echo isset($basePath) ? $basePath : ''; ?>order.php">Order Now</a></li>
        <li class="nav-item"><a class="nav-link" href="<?php echo isset($basePath) ? $basePath : ''; ?>track_order.php">Track Order</a></li>
        <li class="nav-item ms-lg-2 mt-2 mt-lg-0">
          <a class="btn btn-order btn-sm" href="<?php echo isset($basePath) ? $basePath : ''; ?>order.php">Order Now <i class="fa-solid fa-cart-shopping"></i></a>
        </li>
      </ul>
    </div>
  </div>
</nav>
