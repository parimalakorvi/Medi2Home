<?php
session_start();
$title = "Your Cart";

require_once "./functions/database_functions.php";
require_once "./functions/cart_functions.php";
require "./template/header.php";

// User must be logged in
if (!isset($_SESSION['user'])) {
    echo '<div class="alert alert-danger text-center mt-5">
            ⚠️ You need to <a href="Signin.php" class="alert-link">sign in</a> first to view your cart.
          </div>';
    require "./template/footer.php";
    exit;
}

$conn = db_connect();

// Handle adding to cart
if (isset($_POST['medserial'])) {
    $med_serial = $_POST['medserial'];

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array();
        $_SESSION['total_items'] = 0;
        $_SESSION['total_price'] = '0.00';
    }

    if (!isset($_SESSION['cart'][$med_serial])) {
        $_SESSION['cart'][$med_serial] = 1;
    } elseif (isset($_POST['cart'])) {
        $_SESSION['cart'][$med_serial]++;
    }
}

// Handle quantity update
if (isset($_POST['save_change'])) {
    foreach ($_SESSION['cart'] as $serial => $qty) {
        if ($_POST[$serial] == '0') {
            unset($_SESSION['cart'][$serial]);
        } else {
            $_SESSION['cart'][$serial] = (int) $_POST[$serial];
        }
    }
}

if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
    $_SESSION['total_price'] = total_price($_SESSION['cart']);
    $_SESSION['total_items'] = total_items($_SESSION['cart']);
?>
<div class="container my-5">
    <h3 class="fw-bold text-success mb-4 text-center">🛒 Your Shopping Cart</h3>

    <form action="cart.php" method="post">
        <div class="table-responsive shadow-sm">
            <table class="table table-bordered align-middle">
                <thead class="table-success text-center">
                    <tr>
                        <th>Medicine</th>
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
                        <td>
                            <strong><?php echo htmlspecialchars($med['med_name']); ?></strong><br>
                            <small class="text-muted"><?php echo htmlspecialchars($med['med_manufacturer']); ?></small>
                        </td>
                        <td class="text-center"><?php echo number_format($med['med_price'], 2); ?></td>
                        <td class="text-center" style="width:100px;">
                            <input type="number" name="<?php echo $serial; ?>" value="<?php echo $qty; ?>" 
                                   min="0" class="form-control text-center">
                        </td>
                        <td class="text-center fw-bold"><?php echo number_format($qty * $med['med_price'], 2); ?></td>
                    </tr>
                    <?php } ?>
                    <tr class="fw-bold text-center">
                        <td colspan="2">Total Items: <?php echo $_SESSION['total_items']; ?></td>
                        <td colspan="2">Grand Total: ₹<?php echo number_format($_SESSION['total_price'], 2); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between mt-3">
            <button type="submit" name="save_change" class="btn btn-success px-4">💾 Save Changes</button>
            <div>
                <a href="medicines.php" class="btn btn-outline-success me-2">⬅️ Continue Shopping</a>
                <a href="checkout.php" class="btn btn-primary">Proceed to Checkout ➡️</a>
            </div>
        </div>
    </form>
</div>
<?php
} else {
    echo '<div class="alert alert-warning text-center mt-5">
            🛍️ Your cart is empty! Browse <a href="medicines.php" class="alert-link">medicines</a> to add items.
          </div>';
}
?>

<?php
// Purchase History Section
if (isset($_SESSION['user'])) {
    $customer = getCustomerIdbyEmail($_SESSION['email']);
    $customerid = $customer['id'];

    $query = "
        SELECT medicines.med_name, medicines.med_image, cartitems.quantity, cart.date
        FROM cart 
        JOIN cartitems ON cart.id = cartitems.cartid 
        JOIN medicines ON cartitems.productid = medicines.med_serial 
        WHERE cart.customerid = '$customerid'
        ORDER BY cart.date DESC
    ";

    $result = mysqli_query($conn, $query);
    if (mysqli_num_rows($result) > 0) {
        echo '<div class="container my-5">
                <h4 class="fw-bold text-success mb-3">🧾 Purchase History</h4>
                <div class="table-responsive shadow-sm">
                <table class="table table-bordered align-middle">
                    <thead class="table-success text-center">
                        <tr>
                            <th>Image</th>
                            <th>Medicine</th>
                            <th>Quantity</th>
                            <th>Purchase Date</th>
                        </tr>
                    </thead>
                    <tbody>';
        while ($query_row = mysqli_fetch_assoc($result)) {
            echo '<tr>
                    <td class="text-center">
                        <img src="./bootstrap/img/' . htmlspecialchars($query_row['med_image']) . '" 
                             alt="Medicine" width="70" class="img-thumbnail">
                    </td>
                    <td>' . htmlspecialchars($query_row['med_name']) . '</td>
                    <td class="text-center">' . htmlspecialchars($query_row['quantity']) . '</td>
                    <td class="text-center">' . htmlspecialchars($query_row['date']) . '</td>
                </tr>';
        }
        echo '</tbody></table></div></div>';
    }
}

if (isset($conn)) { mysqli_close($conn); }
require "./template/footer.php";
?>
