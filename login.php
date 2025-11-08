<?php
  $title = "Login | Medi2Home";
  require "./template/header.php";
?>

<div class="container py-5" style="min-height: 80vh;">
  <div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
      <div class="card shadow-lg border-0 rounded-4 p-4">
        <div class="text-center mb-4">
          <img src="./bootstrap/img/front.png" alt="💊Medi2Home" width="60">
          <h3 class="mt-3 text-success fw-bold">Welcome Back!</h3>
          <p class="text-muted">Login to continue your health journey</p>
        </div>

        <!-- Login Form -->
        <form method="post" action="user_verify.php">
          <div class="mb-3">
            <label class="form-label fw-semibold">Username (Email)</label>
            <input type="text" class="form-control form-control-lg" name="username" placeholder="Enter your email" required>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Password</label>
            <input type="password" class="form-control form-control-lg" name="password" placeholder="Enter your password" required>
          </div>

          <div class="d-grid mt-4">
            <button type="submit" class="btn btn-success btn-lg">Login</button>
          </div>

          <p class="text-center mt-4 mb-0 text-muted">
            Don’t have an account?
            <a href="signup.php" class="text-decoration-none text-success fw-semibold">Sign Up</a>
          </p>
        </form>

        <!-- Display login error messages (if any) -->
        <?php
          $fullurl = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
          if (strpos($fullurl, "signin=empty") == true) {
            echo "<p class='text-danger text-center mt-3'>⚠️ Please fill in all fields.</p>";
          } elseif (strpos($fullurl, "signin=invalidusername") == true) {
            echo "<p class='text-danger text-center mt-3'>⚠️ Username does not exist.</p>";
          } elseif (strpos($fullurl, "signin=invalidpassword") == true) {
            echo "<p class='text-danger text-center mt-3'>⚠️ Incorrect password.</p>";
          }
        ?>
      </div>
    </div>
  </div>
</div>

<?php require "./template/footer.php"; ?>
