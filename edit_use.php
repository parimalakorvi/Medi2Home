<?php
session_start();

// Ensure form was submitted
if (!isset($_POST['save_change'])) {
    echo "❌ Invalid access!";
    exit;
}

require_once "./functions/database_functions.php";
$conn = db_connect();

// Sanitize input
$usefor = mysqli_real_escape_string($conn, trim($_POST['name']));
$id     = mysqli_real_escape_string($conn, trim($_POST['id']));

// Update query
$query = "UPDATE used_for SET used_for_name = '$usefor' WHERE used_for_id = '$id'";
$result = mysqli_query($conn, $query);

if (!$result) {
    echo "❌ Failed to update use: " . mysqli_error($conn);
    exit;
} else {
    // ✅ Redirect back to admin page with success message
    header("Location: admin_used_for.php?update=success");
    exit;
}

// Close DB connection
if (isset($conn)) {
    mysqli_close($conn);
}
?>
