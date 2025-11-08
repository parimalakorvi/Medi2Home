<?php
session_start();
require_once "./functions/database_functions.php";

// ✅ Redirect if not logged in
if (!isset($_SESSION['user']) || !isset($_SESSION['email'])) {
  header("Location: login.php");
  exit;
}

$conn = db_connect();
$email = $_SESSION['email'];

// ✅ Fetch user details
$query = "SELECT * FROM customers WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $email);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$title = "My Profile";
require "./template/header.php";
?>

<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
      <div class="card shadow border-0">
        <div class="card-header bg-success text-white text-center">
          <h4 class="mb-0"><i class="bi bi-person-circle"></i> My Profile</h4>
        </div>
        <div class="card-body">
          <form method="POST" action="update_profile.php">
            <div class="mb-3">
              <label class="form-label">First Name</label>
              <input type="text" name="firstname" class="form-control" value="<?= htmlspecialchars($user['firstname']); ?>" required>
            </div>

            <div class="mb-3">
              <label class="form-label">Last Name</label>
              <input type="text" name="lastname" class="form-control" value="<?= htmlspecialchars($user['lastname']); ?>" required>
            </div>

            <div class="mb-3">
              <label class="form-label">Email</label>
              <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']); ?>" readonly>
            </div>

            <div class="mb-3">
              <label class="form-label">Address</label>
              <input type="text" name="address" class="form-control" value="<?= htmlspecialchars($user['address']); ?>" required>
            </div>

            <div class="mb-3">
              <label class="form-label">City</label>
              <input type="text" name="city" class="form-control" value="<?= htmlspecialchars($user['city']); ?>" required>
            </div>

            <div class="mb-3">
              <label class="form-label">ZIP Code</label>
              <input type="text" name="zipcode" class="form-control" value="<?= htmlspecialchars($user['zipcode']); ?>" required>
            </div>

            <div class="d-grid mt-4">
              <button type="submit" class="btn btn-success"><i class="bi bi-save"></i> Update Profile</button>
            </div>
          </form>
        </div>
      </div>

      <div class="text-center mt-4">
        <a href="upload_prescription.php" class="btn btn-outline-success me-2"><i class="bi bi-upload"></i> Upload Prescription</a>
        <a href="order_prescription.php" class="btn btn-outline-primary"><i class="bi bi-receipt"></i> My Orders</a>
      </div>
    </div>
  </div>
</div>

<?php
require "./template/footer.php";
?>
