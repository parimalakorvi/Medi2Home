<?php
session_start();

// Check if form was submitted properly
if (!isset($_POST['save_change'])) {
    echo "❌ Invalid access!";
    exit;
}

// Sanitize input
require_once "./functions/database_functions.php";
$conn = db_connect();

$type = mysqli_real_escape_string($conn, trim($_POST['name']));
$id   = mysqli_real_escape_string($conn, trim($_POST['id']));

// Update query
$query = "UPDATE type SET type_name = '$type' WHERE type_id = '$id'";
$result = mysqli_query($conn, $query);

if (!$result) {
    echo "❌ Failed to update type: " . mysqli_error($conn);
    exit;
} else {
    // ✅ Redirect back to types list with success message
    header("Location: admin_types.php?update=success");
    exit;
}

// Close connection
if (isset($conn)) {
    mysqli_close($conn);
}
?>
