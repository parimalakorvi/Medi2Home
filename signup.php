<div style="background: url('https://venngage-wordpress.s3.amazonaws.com/uploads/2018/09/Simple-Minimalist-Background-Image.jpg') no-repeat center center; background-size: cover; min-height: 100vh;">
<?php
$title = "User Sign Up";
require_once "./template/header.php";
?>

<div class="container py-5">
  <div class="card p-4 shadow-lg border-0" style="max-width: 600px; margin: auto; border-radius: 15px; background: rgba(255,255,255,0.9);">
    <h3 class="text-center text-success mb-4">📝 Create an Account</h3>

    <form method="post" action="user_signup.php">
      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label">First Name</label>
          <input type="text" class="form-control" name="firstname" required>
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">Last Name</label>
          <input type="text" class="form-control" name="lastname" required>
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" class="form-control" name="email" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" class="form-control" name="password" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Address</label>
        <input type="text" class="form-control" name="address">
      </div>

      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label">City</label>
          <input type="text" class="form-control" name="city">
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">Zip Code</label>
          <input type="text" class="form-control" name="zipcode">
        </div>
      </div>

      <div class="text-center mt-4">
        <button type="submit" class="btn btn-success px-4">Register</button>
      </div>
    </form>

    <div class="text-center mt-3">
      <small>Already have an account? <a href="login.php" class="text-success fw-bold">Sign In</a></small>
    </div>

    <div class="text-center mt-3">
      <?php
        $fullurl = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        if (strpos($fullurl, "signup=empty") !== false) echo '<p class="text-danger">Please fill all fields.</p>';
        if (strpos($fullurl, "signup=invalidemail") !== false) echo '<p class="text-danger">Invalid email address.</p>';
      ?>
    </div>
  </div>
</div>

<?php require_once "./template/footer.php"; ?>
</div>
