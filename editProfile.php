<?php
session_start();
require_once "./functions/database_functions.php";
$title = "Edit Profile";
require "./template/header.php";

// Ensure user is logged in
if (!isset($_SESSION['user']) || !isset($_SESSION['email'])) {
    echo '<div class="alert alert-danger text-center mt-5">
            ⚠️ Please <a href="Signin.php" class="alert-link">sign in</a> to update your profile.
          </div>';
    require "./template/footer.php";
    exit;
}

$conn = db_connect();

// Sanitize input
$firstname = mysqli_real_escape_string($conn, trim($_POST['firstname']));
$lastname  = mysqli_real_escape_string($conn, trim($_POST['lastname']));
$email     = mysqli_real_escape_string($conn, trim($_POST['email']));
$address   = mysqli_real_escape_string($conn, trim($_POST['address']));
$city      = mysqli_real_escape_string($conn, trim($_POST['city']));
$zipcode   = mysqli_real_escape_string($conn, trim($_POST['zipcode']));

// Fetch current customer ID
$customer  = getCustomerIdbyEmail($_SESSION['email']);
$id = $customer['id'];

// Update user details
$query = "
    UPDATE customers 
    SET firstname = '$firstname', 
        lastname = '$lastname', 
        email = '$email',
        address = '$address',
        city = '$city',
        zipcode = '$zipcode'
    WHERE id = '$id'
";
$result = mysqli_query($conn, $query);

if (!$result) {
    echo '<div class="alert alert-danger text-center mt-5">
            ❌ Failed to update your profile: ' . mysqli_error($conn) . '
          </div>';
} else {
    // Update session email if changed
    $_SESSION['email'] = $email;

    echo '
    <div class="container text-center mt-5">
        <div class="alert alert-success shadow-sm">
            ✅ <strong>Success!</strong> Your profile has been updated successfully.
        </div>
        <p class="text-muted">Redirecting you to the home page...</p>
    </div>
    <script>
        setTimeout(() => {
            window.location.href = "http://localhost/medi2home/index.php";
        }, 3000);
    </script>';
}

if (isset($conn)) {
    mysqli_close($conn);
}

require "./template/footer.php";
?>
