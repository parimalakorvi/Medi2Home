<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= isset($title) ? htmlspecialchars($title) . " | Medi2Home" : "Medi2Home"; ?></title>

  <!-- ✅ Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- ✅ Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  <!-- ✅ Google Font -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

  <!-- ✅ Favicon -->
  <link rel="icon" href="./bootstrap/img/logo.png" type="image/png">

  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f8f9fa;
    }

    .navbar {
      box-shadow: 0 2px 6px rgba(0,0,0,0.08);
      background-color: #fff !important;
    }

    .navbar-brand {
      color: #28a745 !important;
      font-weight: 700;
      font-size: 1.5rem;
    }

    .nav-link {
      color: #555 !important;
      font-weight: 500;
      transition: 0.2s;
    }

    .nav-link:hover {
      color: #28a745 !important;
      text-decoration: underline;
    }

    footer {
      background-color: #fff;
      padding: 1.2rem 0;
      box-shadow: 0 -1px 5px rgba(0,0,0,0.05);
      margin-top: 3rem;
    }

    footer a {
      color: #198754;
      text-decoration: none;
      margin: 0 8px;
    }

    footer a:hover {
      text-decoration: underline;
    }

    .hero-title {
      font-weight: 700;
      text-align: center;
      margin-top: 2rem;
      color: #198754;
    }
  </style>
</head>

<body>
  <!-- ✅ Navbar -->
  <nav class="navbar navbar-expand-lg navbar-light sticky-top">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center" href="index.php">
        <img src="./bootstrap/img/logo.png" alt="Medi2Home" width="45" class="me-2 rounded-circle">
        Medi2Home
      </a>

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto align-items-center">
          <li class="nav-item"><a class="nav-link" href="index.php"><i class="bi bi-house"></i> Home</a></li>
          <li class="nav-item"><a class="nav-link" href="medicines.php"><i class="bi bi-capsule-pill"></i> Medicines</a></li>
          <li class="nav-item"><a class="nav-link" href="contactus.php"><i class="bi bi-envelope"></i> Contact</a></li>

          <?php if (isset($_SESSION['user'])): ?>
            <!-- User Logged In Menu -->
            <li class="nav-item"><a class="nav-link" href="profile.php"><i class="bi bi-person-circle"></i> Profile</a></li>
            <li class="nav-item"><a class="nav-link" href="upload_prescription.php"><i class="bi bi-upload"></i> Upload Prescription</a></li>
            <li class="nav-item"><a class="nav-link" href="order_prescription.php"><i class="bi bi-receipt"></i> My Orders</a></li>
            <li class="nav-item"><a class="nav-link" href="cart.php"><i class="bi bi-cart4"></i> My Cart</a></li>
            <li class="nav-item"><a class="nav-link text-danger" href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
          <?php else: ?>
            <!-- Guest Menu -->
            <li class="nav-item"><a class="nav-link" href="login.php"><i class="bi bi-box-arrow-in-right"></i> Login</a></li>
            <li class="nav-item"><a class="nav-link" href="signup.php"><i class="bi bi-person-plus"></i> Sign Up</a></li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
  </nav>

  <!-- ✅ Page Container Starts -->
  <div class="container mt-4">
