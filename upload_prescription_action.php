<?php
session_start();
require_once "./functions/database_functions.php";
$conn = db_connect();

if (!isset($_SESSION['email'])) {
  header("Location: login.php");
  exit;
}

$email = $_SESSION['email'];

// Fetch customer_id from email
$query = "SELECT id FROM customers WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();
$customer_id = $result['id'];

// File upload settings
$target_dir = "uploads/";
if (!is_dir($target_dir)) {
  mkdir($target_dir, 0777, true);
}

$file_name = basename($_FILES["prescription"]["name"]);
$target_file = $target_dir . time() . "_" . $file_name;
$file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

if (in_array($file_type, ['jpg', 'jpeg', 'png', 'pdf'])) {
  if (move_uploaded_file($_FILES["prescription"]["tmp_name"], $target_file)) {
    // Save to DB
    $query = "INSERT INTO prescriptions (customer_id, file_name) VALUES (?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("is", $customer_id, $target_file);
    $stmt->execute();
    $_SESSION['msg'] = "Prescription uploaded successfully!";
    header("Location: order_prescription.php");
  } else {
    echo "❌ Failed to upload file.";
  }
} else {
  echo "❌ Only JPG, PNG, or PDF files are allowed.";
}

$conn->close();
?>
