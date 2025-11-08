<?php
session_start();
require "./functions/database_functions.php";
$conn = db_connect();

$firstname = trim($_POST['firstname']);
$lastname  = trim($_POST['lastname']);
$email     = trim($_POST['email']);
$password  = trim($_POST['password']);
$address   = trim($_POST['address']);
$city      = trim($_POST['city']);
$zipcode   = trim($_POST['zipcode']);

// ✅ Validation
if (empty($firstname) || empty($lastname) || empty($email) || empty($password) || empty($address) || empty($city) || empty($zipcode)) {
    header("Location: signup.php?signup=empty");
    exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: signup.php?signup=invalidemail");
    exit;
}

// ✅ Check for existing user
$checkQuery = "SELECT id FROM customers WHERE email = ?";
$stmt = $conn->prepare($checkQuery);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    header("Location: signin.php");
    exit;
}

// ✅ Hash password before saving
$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

// ✅ Insert new user
$insertQuery = "INSERT INTO customers (firstname, lastname, email, address, password, city, zipcode) VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($insertQuery);
$stmt->bind_param("sssssss", $firstname, $lastname, $email, $address, $hashedPassword, $city, $zipcode);
if ($stmt->execute()) {
    header("Location: signin.php");
    exit;
} else {
    die("Error: " . $stmt->error);
}

$stmt->close();
$conn->close();
?>
