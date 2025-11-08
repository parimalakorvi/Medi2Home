<?php
$servername = "localhost";
$username = "root";
$password = "";  // leave blank if you didn't set a password
$database = "medi2home"; // must match your phpMyAdmin database name

$conn = mysqli_connect($servername, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
