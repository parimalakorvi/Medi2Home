<?php
session_start();
if (!isset($_POST['save_change'])) {
    echo "❌ Invalid access!";
    exit;
}

require_once "./functions/database_functions.php";
$conn = db_connect();

// Sanitize and validate inputs
$serial        = mysqli_real_escape_string($conn, trim($_POST['serial']));
$name          = mysqli_real_escape_string($conn, trim($_POST['name']));
$manufacturer  = mysqli_real_escape_string($conn, trim($_POST['manufacturer']));
$descr         = mysqli_real_escape_string($conn, trim($_POST['descr']));
$price         = floatval(trim($_POST['price']));
$usefor        = mysqli_real_escape_string($conn, trim($_POST['usefor']));
$type          = mysqli_real_escape_string($conn, trim($_POST['type']));
$image         = null;

// Handle image upload if provided
if (isset($_FILES['image']) && $_FILES['image']['name'] != "") {
    $image = basename($_FILES['image']['name']);
    $uploadPath = $_SERVER['DOCUMENT_ROOT'] . str_replace(basename($_SERVER['PHP_SELF']), '', $_SERVER['PHP_SELF']) . "bootstrap/img/" . $image;

    if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
        echo "⚠️ Failed to upload image. Check folder permissions.";
        exit;
    }
}

/* ---------- FIND OR CREATE 'USE FOR' ---------- */
$findUseQuery = "SELECT used_for_id FROM used_for WHERE used_for_name = '$usefor'";
$findUseResult = mysqli_query($conn, $findUseQuery);
if (!$findUseResult) {
    echo "❌ Database error: " . mysqli_error($conn);
    exit;
}

if (mysqli_num_rows($findUseResult) == 0) {
    $insertUse = "INSERT INTO used_for(used_for_name) VALUES ('$usefor')";
    mysqli_query($conn, $insertUse);
    $used_for_id = mysqli_insert_id($conn);
} else {
    $useRow = mysqli_fetch_assoc($findUseResult);
    $used_for_id = $useRow['used_for_id'];
}

/* ---------- FIND OR CREATE 'TYPE' ---------- */
$findTypeQuery = "SELECT type_id FROM type WHERE type_name = '$type'";
$findTypeResult = mysqli_query($conn, $findTypeQuery);
if (!$findTypeResult) {
    echo "❌ Database error: " . mysqli_error($conn);
    exit;
}

if (mysqli_num_rows($findTypeResult) == 0) {
    $insertType = "INSERT INTO type(type_name) VALUES ('$type')";
    mysqli_query($conn, $insertType);
    $type_id = mysqli_insert_id($conn);
} else {
    $typeRow = mysqli_fetch_assoc($findTypeResult);
    $type_id = $typeRow['type_id'];
}

/* ---------- UPDATE MEDICINE ---------- */
$updateQuery = "
    UPDATE medicines 
    SET 
        med_name = '$name',
        med_manufacturer = '$manufacturer',
        med_descr = '$descr',
        med_price = '$price',
        used_for_id = '$used_for_id',
        type_id = '$type_id'";

if ($image) {
    $updateQuery .= ", med_image = '$image'";
}

$updateQuery .= " WHERE med_serial = '$serial'";

$result = mysqli_query($conn, $updateQuery);

if (!$result) {
    echo "❌ Failed to update medicine: " . mysqli_error($conn);
    exit;
}

// ✅ Redirect back to medicine list with success message
header("Location: admin_med.php?update=success");
exit;

if (isset($conn)) { mysqli_close($conn); }
?>
