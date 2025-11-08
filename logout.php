<?php
// Start session (required to destroy it)
session_start();

// Unset all session variables
$_SESSION = [];

// Destroy session data on the server
session_destroy();

// Redirect to home page with success message
header("Location: index.php?logout=success");
exit;
?>
