<?php
require_once '../includes/db.php';
if (session_status() === PHP_SESSION_NONE) session_start();

if (isset($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $error = 'Please enter both username and password.';
    } else {
        $stmt = $conn->prepare("SELECT * FROM admin WHERE username = ?");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $admin = $stmt->get_result()->fetch_assoc();

        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['full_name'];
            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Invalid username or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login | Scoop Haven</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css/style.css">
<style>body{background:linear-gradient(135deg,#ffe3ec,#fff8f0);}</style>
</head>
<body>

<div class="login-box">
  <div class="text-center mb-4">
    <i class="fa-solid fa-ice-cream fa-2x" style="color:var(--pink-dark);"></i>
    <h4 class="mt-2 mb-0">Scoop Haven</h4>
    <small class="text-muted">Admin Panel Login</small>
  </div>

  <?php if ($error): ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
  <?php endif; ?>

  <form method="POST" action="login.php" class="needs-validation" novalidate>
    <div class="mb-3">
      <label class="form-label">Username</label>
      <input type="text" name="username" class="form-control" required autofocus>
    </div>
    <div class="mb-3">
      <label class="form-label">Password</label>
      <input type="password" name="password" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-hero w-100">Login <i class="fa-solid fa-right-to-bracket"></i></button>
  </form>
  <p class="text-center small text-muted mt-3 mb-0">Default: admin / admin123</p>
  <p class="text-center mt-3"><a href="../index.php"><i class="fa-solid fa-arrow-left"></i> Back to Website</a></p>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
