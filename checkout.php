<?php
session_start();
require_once "./functions/database_functions.php";
$title = "Checkout";
require "./template/header.php";

if (!isset($_SESSION['user'])) {
    echo '<div class="alert alert-danger text-center mt-5">
            ⚠️ You need to <a href="Signin.php" class="alert-link">sign in</a> first to continue checkout.
          </div>';
    require "./template/footer.php";
    exit;
}

if (!isset($_SESSION['cart']) || count($_SESSION['cart']) == 0) {
    echo '<div class="alert alert-warning text-center mt-5">
            🛒 Your cart is empty! Please <a href="medicines.php" class="alert-link">add some medicines</a> before checkout.
          </div>';
    require "./template/footer.php";
    exit;
}

$conn = db_connect();
$_SESSION['total_price'] = total_price($_SESSION['cart']);
$_SESSION['total_items'] = total_items($_SESSION['cart']);
?>

<div class="container my-5">
    <h3 class="fw-bold text-success mb-4 text-center">💳 Checkout Summary</h3>

    <div class="table-responsive shadow-sm">
        <table class="table table-bordered align-middle">
            <thead class="table-success text-center">
                <tr>
                    <th>Medicine</th>
                    <th>Manufacturer</th>
                    <th>Price (₹)</th>
                    <th>Quantity</th>
                    <th>Total (₹)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($_SESSION['cart'] as $serial => $qty) {
                    $med = mysqli_fetch_assoc(getmedBySerial($conn, $serial));
                ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($med['med_name']); ?></strong></td>
                    <td><?php echo htmlspecialchars($med['med_manufacturer']); ?></td>
                    <td class="text-center"><?php echo number_format($med['med_price'], 2); ?></td>
                    <td class="text-center"><?php echo $qty; ?></td>
                    <td class="text-center fw-bold"><?php echo number_format($qty * $med['med_price'], 2); ?></td>
                </tr>
                <?php } ?>
                <tr class="fw-bold text-center">
                    <td colspan="3">Total Items: <?php echo $_SESSION['total_items']; ?></td>
                    <td colspan="2" class="text-success">Grand Total: ₹<?php echo number_format($_SESSION['total_price'], 2); ?></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="text-center mt-4">
        <form method="post" action="purchase.php" onsubmit="return confirmPurchase();" class="d-inline">
            <button type="submit" name="submit" class="btn btn-success px-5 py-2">✅ Confirm Purchase</button>
        </form>
        <a href="cart.php" class="btn btn-outline-primary px-4 py-2 ms-2">🛒 Edit Cart</a>
        <a href="medicines.php" class="btn btn-outline-secondary px-4 py-2 ms-2">⬅️ Continue Shopping</a>
    </div>

    <p class="text-muted text-center mt-3">
        Review your order details carefully before confirming your purchase.
    </p>
</div>

<script>
function confirmPurchase() {
    return confirm("Are you sure you want to confirm your purchase?");
}
</script>

<?php
if (isset($conn)) {
    mysqli_close($conn);
}
require "./template/footer.php";
?>
