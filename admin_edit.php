<?php
session_start();
if((!isset($_SESSION['manager']) && !isset($_SESSION['expert']))) {
	header("Location:index.php");
	exit;
}

$title = "Edit Medicine";
require_once "./template/header.php";
require_once "./functions/database_functions.php";
$conn = db_connect();

if(isset($_GET['medserial'])) {
	$med_serial = mysqli_real_escape_string($conn, $_GET['medserial']);
} else {
	echo "<div class='alert alert-danger text-center mt-5'>❌ Invalid request: Missing medicine serial number.</div>";
	exit;
}

// Fetch medicine details
$query = "SELECT * FROM medicines WHERE med_serial = '$med_serial'";
$result = mysqli_query($conn, $query);
if(!$result || mysqli_num_rows($result) == 0) {
	echo "<div class='alert alert-danger text-center mt-5'>❌ Medicine not found.</div>";
	exit;
}
$row = mysqli_fetch_assoc($result);

// Handle form submission
if(isset($_POST['save_change'])) {
	$name = mysqli_real_escape_string($conn, trim($_POST['name']));
	$manufacturer = mysqli_real_escape_string($conn, trim($_POST['manufacturer']));
	$descr = mysqli_real_escape_string($conn, trim($_POST['descr']));
	$price = floatval(trim($_POST['price']));
	$usefor = mysqli_real_escape_string($conn, trim($_POST['usefor']));
	$type = mysqli_real_escape_string($conn, trim($_POST['type']));

	// Image handling
	$image = $row['med_image']; // Keep old image by default
	if(isset($_FILES['image']) && $_FILES['image']['name'] != "") {
		$newImage = basename($_FILES['image']['name']);
		$uploadDir = $_SERVER['DOCUMENT_ROOT'] . str_replace(basename($_SERVER['PHP_SELF']), '', $_SERVER['PHP_SELF']) . "bootstrap/img/";
		$uploadPath = $uploadDir . $newImage;
		if(move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
			$image = $newImage; // Update image name
		}
	}

	// Ensure use exists or create it
	$findUse = "SELECT used_for_id FROM used_for WHERE used_for_name = '$usefor'";
	$useResult = mysqli_query($conn, $findUse);
	if(mysqli_num_rows($useResult) == 0) {
		mysqli_query($conn, "INSERT INTO used_for(used_for_name) VALUES ('$usefor')");
		$use_id = mysqli_insert_id($conn);
	} else {
		$useRow = mysqli_fetch_assoc($useResult);
		$use_id = $useRow['used_for_id'];
	}

	// Ensure type exists or create it
	$findType = "SELECT type_id FROM type WHERE type_name = '$type'";
	$typeResult = mysqli_query($conn, $findType);
	if(mysqli_num_rows($typeResult) == 0) {
		mysqli_query($conn, "INSERT INTO type(type_name) VALUES ('$type')");
		$type_id = mysqli_insert_id($conn);
	} else {
		$typeRow = mysqli_fetch_assoc($typeResult);
		$type_id = $typeRow['type_id'];
	}

	// Update query
	$updateQuery = "
		UPDATE medicines 
		SET med_name = '$name',
			med_manufacturer = '$manufacturer',
			med_image = '$image',
			med_descr = '$descr',
			med_price = '$price',
			used_for_id = '$use_id',
			type_id = '$type_id'
		WHERE med_serial = '$med_serial'";

	$updateResult = mysqli_query($conn, $updateQuery);

	if(!$updateResult) {
		echo "<div class='alert alert-danger text-center mt-5'>❌ Failed to update medicine: " . mysqli_error($conn) . "</div>";
	} else {
		echo "<div class='alert alert-success text-center mt-5'>✅ Medicine details updated successfully!</div>";
		header("Refresh:2; url=admin_med.php");
	}
}
?>

<div class="container my-5">
	<div class="card shadow-sm border-0 rounded-4">
		<div class="card-header bg-success text-white text-center">
			<h4 class="mb-0">Edit Medicine Details</h4>
		</div>

		<div class="card-body">
			<form method="post" action="admin_edit.php?medserial=<?php echo $row['med_serial']; ?>" enctype="multipart/form-data" class="row g-3">
				
				<div class="col-md-6">
					<label class="form-label">Serial No</label>
					<input type="text" name="serial" class="form-control" value="<?php echo $row['med_serial']; ?>" readonly>
				</div>

				<div class="col-md-6">
					<label class="form-label">Medicine Name</label>
					<input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($row['med_name']); ?>" required>
				</div>

				<div class="col-md-6">
					<label class="form-label">Manufacturer</label>
					<input type="text" name="manufacturer" class="form-control" value="<?php echo htmlspecialchars($row['med_manufacturer']); ?>" required>
				</div>

				<div class="col-md-6">
					<label class="form-label">Price (₹)</label>
					<input type="number" step="0.01" name="price" class="form-control" value="<?php echo $row['med_price']; ?>" required>
				</div>

				<div class="col-md-12">
					<label class="form-label">Description</label>
					<textarea name="descr" class="form-control" rows="4"><?php echo htmlspecialchars($row['med_descr']); ?></textarea>
				</div>

				<div class="col-md-6">
					<label class="form-label">Used For</label>
					<input type="text" name="usefor" class="form-control" value="<?php echo getuseName($conn, $row['used_for_id']); ?>" required>
				</div>

				<div class="col-md-6">
					<label class="form-label">Type</label>
					<input type="text" name="type" class="form-control" value="<?php echo gettypeName($conn, $row['type_id']); ?>" required>
				</div>

				<div class="col-md-6">
					<label class="form-label">Current Image</label><br>
					<img src="./bootstrap/img/<?php echo $row['med_image']; ?>" alt="Medicine Image" width="100" class="img-thumbnail mb-2">
					<input type="file" name="image" class="form-control">
				</div>

				<div class="col-12 text-center mt-4">
					<input type="submit" name="save_change" value="Save Changes" class="btn btn-success px-5">
					<a href="admin_med.php" class="btn btn-outline-secondary ms-2 px-4">Cancel</a>
				</div>
			</form>
		</div>
	</div>
</div>

<?php
if(isset($conn)) { mysqli_close($conn); }
require "./template/footer.php";
?>
