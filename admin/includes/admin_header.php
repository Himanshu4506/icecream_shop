<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) . ' - Admin' : 'Admin Panel'; ?> | Scoop Haven</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css/style.css">
</head>
<body style="background:#f7f3ee;">

<div class="d-flex">
  <!-- SIDEBAR -->
  <div class="admin-sidebar" style="width:230px; flex-shrink:0;">
    <div class="p-3 text-center border-bottom border-secondary-subtle">
      <h5 class="mb-0"><i class="fa-solid fa-ice-cream"></i> Scoop Haven</h5>
      <small>Admin Panel</small>
    </div>
    <a href="dashboard.php" class="<?php echo ($active ?? '') === 'dashboard' ? 'active' : ''; ?>"><i class="fa-solid fa-gauge me-2"></i> Dashboard</a>
    <a href="products.php" class="<?php echo ($active ?? '') === 'products' ? 'active' : ''; ?>"><i class="fa-solid fa-ice-cream me-2"></i> Manage Products</a>
    <a href="orders.php" class="<?php echo ($active ?? '') === 'orders' ? 'active' : ''; ?>"><i class="fa-solid fa-receipt me-2"></i> Manage Orders</a>
    <a href="customers.php" class="<?php echo ($active ?? '') === 'customers' ? 'active' : ''; ?>"><i class="fa-solid fa-users me-2"></i> Customers</a>
    <a href="../index.php" target="_blank"><i class="fa-solid fa-arrow-up-right-from-square me-2"></i> View Site</a>
    <a href="logout.php"><i class="fa-solid fa-right-from-bracket me-2"></i> Logout</a>
  </div>

  <!-- MAIN -->
  <div class="flex-grow-1">
    <div class="admin-topbar d-flex justify-content-between align-items-center">
      <h5 class="mb-0"><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) : 'Dashboard'; ?></h5>
      <div>
        <i class="fa-solid fa-user-shield me-2"></i>
        <?php echo htmlspecialchars($_SESSION['admin_name'] ?? 'Admin'); ?>
      </div>
    </div>
    <div class="p-4">
