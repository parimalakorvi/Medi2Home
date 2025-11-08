<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
$title = "Welcome to Medi2Home";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo isset($title) ? htmlspecialchars($title) : 'Medi2Home'; ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" href="./bootstrap/img/logo.png" type="image/png">
  <style>
    body {
      background-color: #ffffff;
      font-family: 'Poppins', sans-serif;
    }
    nav.navbar {
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
    .nav-link:hover {
      color: #198754 !important;
    }

    /* Hero section with text above image */
    .hero {
      position: relative;
      text-align: center;
      padding: 100px 20px 40px;
      background-color: #ffffff;
    }

    .hero h1 {
      font-size: 3rem;
      font-weight: bold;
      color: #007bff;
      margin-bottom: 20px;
    }

    .hero p {
      font-size: 1.2rem;
      color: #333;
      margin-bottom: 40px;
    }

    .hero img {
      max-width: 500px;
      width: 80%;
      height: auto;
      margin: 0 auto;
      display: block;
    }

    .btn-success {
      font-size: 1.1rem;
      padding: 0.75rem 2rem;
      border-radius: 30px;
    }

    footer {
      background-color: #fff;
      box-shadow: 0 -1px 4px rgba(0,0,0,0.1);
    }
  </style>
</head>
<body>

<!-- ====== Navbar ====== -->
<nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top">
  <div class="container">
    <a class="navbar-brand fw-bold text-success d-flex align-items-center" href="index.php">
      <img src="./bootstrap/img/logo.png" alt="Medi2Home Logo" width="50" class="me-2 rounded-circle">
      Medi2Home
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto align-items-center">
        <li class="nav-item"><a class="nav-link" href="index.php">🏠 Home</a></li>
        <li class="nav-item"><a class="nav-link" href="medicines.php">💊 Medicines</a></li>
        <li class="nav-item"><a class="nav-link" href="contactus.php">📞 Contact</a></li>

        <?php if (isset($_SESSION['user'])): ?>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="userMenu" role="button" data-bs-toggle="dropdown">
              👤 <?php echo htmlspecialchars($_SESSION['user']); ?>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><a class="dropdown-item" href="profile.php">My Profile</a></li>
              <li><a class="dropdown-item" href="cart.php">My Cart</a></li>
              <li><a class="dropdown-item" href="empty_session.php">Logout</a></li>
            </ul>
          </li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="login.php">🔐 Login</a></li>
          <li class="nav-item"><a class="nav-link" href="signup.php">📝 Sign Up</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<!-- ====== Hero Section ====== -->
<section class="hero">
  <h1>Welcome to Medi2Home</h1>
  <p>Your trusted online pharmacy — order medicines easily from home.</p>
  <img src="./bootstrap/img/logo.png" alt="Medi2Home Main Logo">
  <a href="medicines.php" class="btn btn-success btn-lg mt-4">Shop Now</a>
</section>

<!-- ====== Info Section ====== -->
<div class="container text-center mt-5 mb-5">
  <h3 class="text-primary mb-3">Why Choose Medi2Home?</h3>
  <p class="text-muted">
    ✅ 24/7 Online Ordering &nbsp; | &nbsp;
    💊 Wide Range of Medicines &nbsp; | &nbsp;
    🚚 Fast & Reliable Delivery &nbsp; | &nbsp;
    💳 Secure Payments
  </p>
</div>

<!-- ====== Footer ====== -->
<footer class="text-center py-3 border-top">
  <p class="mb-0 text-muted">&copy; 2025 <span class="text-success fw-bold">Medi2Home</span> — Your trusted online pharmacy 💊</p>
  <small>
    <a href="index.php" class="text-decoration-none text-success">Home</a> |
    <a href="medicines.php" class="text-decoration-none text-success">Medicines</a> |
    <a href="contactus.php" class="text-decoration-none text-success">Contact</a> |
    <a href="login.php" class="text-decoration-none text-success">Login</a>
  </small>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
