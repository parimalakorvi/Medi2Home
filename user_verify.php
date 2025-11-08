<?php
session_start();
require_once "./functions/database_functions.php";
$conn = db_connect();

$username = trim($_POST['username']);
$password = trim($_POST['password']);

if (empty($username) || empty($password)) {
    header("Location: login.php?login=empty");
    exit;
}

// ✅ Check for manager
$query = "SELECT * FROM manager WHERE name = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$manager = $stmt->get_result()->fetch_assoc();

if ($manager && $manager['pass'] === $password) {
    $_SESSION['manager'] = true;
    unset($_SESSION['expert'], $_SESSION['user'], $_SESSION['email']);
    header("Location: admin_med.php");
    exit;
}

// ✅ Check for expert
$query = "SELECT * FROM expert WHERE name = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$expert = $stmt->get_result()->fetch_assoc();

if ($expert && $expert['pass'] === $password) {
    $_SESSION['expert'] = true;
    unset($_SESSION['manager'], $_SESSION['user'], $_SESSION['email']);
    header("Location: admin_med.php");
    exit;
}

// ✅ Check for regular user (email/password)
$query = "SELECT * FROM customers WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user'] = true;
    $_SESSION['email'] = $username;
    unset($_SESSION['manager'], $_SESSION['expert']);
    header("Location: index.php");
    exit;
}

// ❌ If all fail
header("Location: login.php?login=invalidusername");
exit;
?>
