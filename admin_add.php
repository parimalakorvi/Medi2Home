<?php
session_start();
if((!isset($_SESSION['manager']) && !isset($_SESSION['expert']))) {
	header("Location:index.php");
	exit;
}

$title = "Add New Medicine";
require "./template/header.php";
require "./functions/database_functions.php";
$conn = db_connect();

if(isset($_POST['add'])) {
	// Get form inputs safely
	$serial = mysqli_real_escape_string($conn, trim($_POST['serial']));
	$medname = mysqli_real_escape_string($conn, trim($_POST['title']));
	$manufacturer = mysqli_real_escape_string($conn, trim($_POST['manufacturer']));
	$descr = mysqli_real_escape_string($conn, trim($_POST['descr']));
	$price = floatval(trim($_POST['price']));
	$use = mysqli_real_escape_string($conn, trim($_POST['use_for']));
	$type = mysqli_real_escape_string($conn, trim($_POST['type']));

	// Handle image upload
	$image = "";
	if(isset($_FILES['image']) && $_FILES['image']['name'] != "") {
		$image = basename($_FILES['image']['name']);
		$uploadDir = $_SERVER['DOCUMENT_ROOT'] . str_replace(basename($_SERVER['PHP_SELF']), '', $_SERVER['PHP_SELF']) . "bootstrap/img/";
		$uploadPath = $uploadDir . $image;

		if(!move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
			echo "<div class='alert alert-danger text-center'>❌ Image upload failed. Please check folder permissions.</div>";
			exit;
		}
	}

	// Check if 'use' exists, else insert
	$finduse = "SELECT * FROM used_for WHERE used_for_name = '$use'";
	$findResult = mysqli_query($conn, $finduse);
	if(mysqli_num_rows($findResult) == 0) {
		$insertuse = "INSERT INTO used_for(used_for_name) VALUES ('$use')";
		if(!mysqli_query($conn, $insertuse)) {
			echo "Can't add new Uses " . mysqli_error($conn);
			exit;
		}
		$used_for_id = mysqli_insert_id($conn);
	} else {
		$row = mysqli_fetch_assoc($findResult);
		$used_for_id = $row['used_for_id'];
	}

	// Check if 'type' exists, else insert
	$findtype = "SELECT * FROM type WHERE type_name = '$type'";
	$findResult = mysqli_query($conn, $findtype);
	if(mysqli_num_rows($findResult) == 0) {
		$inserttype = "INSERT INTO type(type_name) VALUES ('$type')";
		if(!mysqli_query($conn, $inserttype)) {
			echo "Can't add new Type " . mysqli_error($conn);
			exit;
		}
		$type_id = mysqli_insert_id($conn);
	} else {
		$row = mysqli_fetch_assoc($findResult);
		$type_id = $row['type_id'];
	}

	// Insert into medicines table
	$query = "INSERT INTO medicines (med_serial, med_name, med_manufacturer, med_image, med_descr, med_price, used_for_id, type_id)
			  VALUES ('$serial', '$medname', '$manufacturer', '$image', '$descr', '$price', '$used_for_id', '$type_id')";
	$result = mysqli_query($conn, $query);

	if(!$result) {
		echo "<div class='alert alert-danger text-center'>❌ Can't add new medicine: " . mysqli_error($conn) . "</div>";
		exit;
	} else {
		echo "<div class='alert alert-success text-center'>✅ Medicine added successfully!</div>";
		header("Refresh:2; url=admin_med.php");
	}
}
?>

<div class="container my-5">
	<div class="card shadow-sm border-0 rounded-4">
		<div class="card-header bg-success text-white text-center">
			<h4 class="mb-0">Add New Medicine</h4>
		</div>
		<div class="card-body">
			<form method="post" action="admin_add.php" enctype="multipart/form-data" class="row g-3">
				
				<div class="col-md-6">
					<label class="form-label">Serial No</label>
					<input type="text" name="serial" class="form-control" required>
				</div>

				<div class="col-md-6">
					<label class="form-label">Medicine Name</label>
					<input type="text" name="title" class="form-control" required>
				</div>

				<div class="col-md-6">
					<label class="form-label">Manufacturer</label>
					<input type="text" name="manufacturer" class="form-control" required>
				</div>

				<div class="col-md-6">
					<label class="form-label">Price (₹)</label>
					<input type="number" step="0.01" name="price" class="form-control" required>
				</div>

				<div class="col-md-6">
					<label class="form-label">Used For</label>
					<input type="text" name="use_for" class="form-control" placeholder="e.g., Fever, Pain, Cold" required>
				</div>

				<div class="col-md-6">
					<label class="form-label">Type</label>
					<input type="text" name="type" class="form-control" placeholder="e.g., Tablet, Syrup" required>
				</div>

				<div class="col-md-12">
					<label class="form-label">Description</label>
					<textarea name="descr" class="form-control" rows="4" placeholder="Enter detailed medicine description"></textarea>
				</div>

				<div class="col-md-6">
					<label class="form-label">Upload Image</label>
					<input type="file" name="image" class="form-control">
				</div>

				<div class="col-12 text-center mt-4">
					<input type="submit" name="add" value="Add Medicine" class="btn btn-success px-5">
					<a href="admin_med.php" class="btn btn-outline-secondary ms-2 px-4">Cancel</a>
				</div>
			</form>
		</div>
	</div>
</div>

<?php
if(isset($conn)) { mysqli_close($conn); }
require_once "./template/footer.php";
?>
