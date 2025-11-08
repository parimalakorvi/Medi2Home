<?php
session_start();
require_once "./functions/database_functions.php";

$title = "Purchase Summary";
require "./template/header.php";

// ✅ Ensure user logged in
if (!isset($_SESSION['email'])) {
    echo '<div class="alert alert-danger text-center mt-5">⚠️ Please log in to continue your purchase.</div>';
    require "./template/footer.php";
    exit;
}

// ✅ Check cart
if (!isset($_SESSION['cart']) || count($_SESSION['cart']) == 0) {
    echo '<div class="alert alert-warning text-center mt-5">🛒 Your cart is empty! Please add items to proceed.</div>';
    require "./template/footer.php";
    exit;
}

$conn = db_connect();
$customer = getCustomerIdbyEmail($_SESSION['email']);
?>

<div class="container mt-5">
  <h3 class="text-success fw-bold text-center mb-4">🧾 Order Summary</h3>
  <div class="table-responsive">
    <table class="table table-bordered table-striped text-center align-middle shadow-sm">
      <thead class="table-success">
        <tr>
          <th>Item</th>
          <th>Manufacturer</th>
          <th>Price</th>
          <th>Quantity</th>
          <th>Total</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $subtotal = 0;
        foreach ($_SESSION['cart'] as $serial => $qty) {
            $med = mysqli_fetch_assoc(getmedBySerial($conn, $serial));
            $itemTotal = $qty * $med['med_price'];
            $subtotal += $itemTotal;
        ?>
        <tr>
          <td><?php echo htmlspecialchars($med['med_name']); ?></td>
          <td><?php echo htmlspecialchars($med['med_manufacturer']); ?></td>
          <td>₹<?php echo number_format($med['med_price'], 2); ?></td>
          <td><?php echo (int)$qty; ?></td>
          <td>₹<?php echo number_format($itemTotal, 2); ?></td>
        </tr>
        <?php } ?>
      </tbody>
      <tfoot class="table-light fw-bold">
        <tr>
          <td colspan="4" class="text-end">Subtotal:</td>
          <td>₹<?php echo number_format($subtotal, 2); ?></td>
        </tr>
        <tr>
          <td colspan="4" class="text-end">Shipping:</td>
          <td>₹20.00</td>
        </tr>
        <tr class="table-success">
          <td colspan="4" class="text-end">Grand Total:</td>
          <td>₹<?php echo number_format($subtotal + 20, 2); ?></td>
        </tr>
      </tfoot>
    </table>
  </div>

  <hr class="my-4">

  <h4 class="text-center text-success mb-3">🏠 Shipping Information</h4>
  <form method="post" action="process.php" class="p-3 border rounded bg-light shadow-sm">
    <div class="row">
      <div class="col-md-6 mb-3">
        <label class="form-label">First Name</label>
        <input type="text" class="form-control" name="firstname" value="<?php echo htmlspecialchars($customer['firstname']); ?>" required>
      </div>
      <div class="col-md-6 mb-3">
        <label class="form-label">Last Name</label>
        <input type="text" class="form-control" name="lastname" value="<?php echo htmlspecialchars($customer['lastname']); ?>" required>
      </div>
    </div>

    <div class="mb-3">
      <label class="form-label">Address</label>
      <input type="text" class="form-control" name="address" value="<?php echo htmlspecialchars($customer['address']); ?>" required>
    </div>

    <div class="row">
      <div class="col-md-6 mb-3">
        <label class="form-label">City</label>
        <input type="text" class="form-control" name="city" value="<?php echo htmlspecialchars($customer['city']); ?>" required>
      </div>
      <div class="col-md-6 mb-3">
        <label class="form-label">Zip Code</label>
        <input type="text" class="form-control" name="zipcode" value="<?php echo htmlspecialchars($customer['zipcode']); ?>" required>
      </div>
    </div>

    <div class="text-center mt-4">
      <button type="reset" class="btn btn-secondary me-2">Cancel</button>
      <button type="submit" class="btn btn-success px-4">Confirm Purchase</button>
    </div>
  </form>
</div>

<?php
if (isset($conn)) { mysqli_close($conn); }
require "./template/footer.php";
?>
