<?php
session_start();
require_once "./functions/database_functions.php";

$title = "Purchase Confirmation";
require "./template/header.php";

$conn = db_connect();

// ✅ Ensure the user is logged in
if (!isset($_SESSION['email']) || !isset($_SESSION['cart'])) {
    echo '<div class="alert alert-danger text-center mt-5">⚠️ Please sign in and add items to your cart before checking out.</div>';
    require "./template/footer.php";
    exit;
}

// ✅ Clean user input
$firstname = mysqli_real_escape_string($conn, trim($_POST['firstname']));
$lastname  = mysqli_real_escape_string($conn, trim($_POST['lastname']));
$address   = mysqli_real_escape_string($conn, trim($_POST['address']));
$city      = mysqli_real_escape_string($conn, trim($_POST['city']));
$zipcode   = mysqli_real_escape_string($conn, trim($_POST['zipcode']));

// ✅ Get customer ID
$customer = getCustomerIdbyEmail($_SESSION['email']);
$customerid = $customer['id'];

// ✅ Update user info
$query = "
    UPDATE customers SET 
        firstname='$firstname', 
        lastname='$lastname', 
        address='$address', 
        city='$city', 
        zipcode='$zipcode'
    WHERE id='$customerid'
";
mysqli_query($conn, $query);

// ✅ Create new cart entry
$date = date("Y-m-d H:i:s");
insertIntoCart($conn, $customerid, $date);

// ✅ Get cart ID
$cartid = getCartId($conn, $customerid);

// ✅ Insert each item into cartitems
foreach ($_SESSION['cart'] as $serial => $qty) {
    $query = "
        INSERT INTO cartitems(cartid, productid, quantity) 
        VALUES ('$cartid', '$serial', '$qty')
    ";
    $result = mysqli_query($conn, $query);
    if (!$result) {
        die("<div class='alert alert-danger text-center mt-5'>❌ Error while saving your cart: " . mysqli_error($conn) . "</div>");
    }
}

// ✅ Clear session cart
unset($_SESSION['cart']);
unset($_SESSION['total_items']);
unset($_SESSION['total_price']);
?>

<!-- ===== Confirmation Message ===== -->
<div class="container text-center mt-5">
  <div class="alert alert-success py-4 shadow-sm">
    <h3 class="fw-bold text-success">🎉 Order Confirmed!</h3>
    <p class="lead mb-3">Your order has been placed successfully.</p>
    <p class="text-muted">You will be redirected to the homepage shortly...</p>
  </div>
</div>

<script>
setTimeout(function(){
  window.location.href = "index.php";
}, 3000);
</script>

<?php
if (isset($conn)) mysqli_close($conn);
require "./template/footer.php";
?>
