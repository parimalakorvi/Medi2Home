<?php
session_start();
if((!isset($_SESSION['manager']) && !isset($_SESSION['expert']))) {
	header("Location:index.php");
	exit;
}

$title = "Edit Type";
require_once "./template/header.php";
require_once "./functions/database_functions.php";
$conn = db_connect();

// Check if typeid is provided
if(isset($_GET['typeid'])) {
	$typeid = mysqli_real_escape_string($conn, $_GET['typeid']);
} else {
	echo "<div class='alert alert-danger text-center mt-5'>❌ Invalid request. Missing type ID.</div>";
	exit;
}

// Fetch existing type details
$query = "SELECT * FROM type WHERE type_id = '$typeid'";
$result = mysqli_query($conn, $query);
if(!$result || mysqli_num_rows($result) == 0) {
	echo "<div class='alert alert-danger text-center mt-5'>❌ Type not found.</div>";
	exit;
}
$row = mysqli_fetch_assoc($result);

// Handle form submission
if(isset($_POST['save_change'])) {
	$newName = trim($_POST['name']);
	$newName = mysqli_real_escape_string($conn, $newName);
	$id = mysqli_real_escape_string($conn, $_POST['id']);

	// Check if another type with the same name exists
	$checkQuery = "SELECT * FROM type WHERE type_name = '$newName' AND type_id != '$id'";
	$checkResult = mysqli_query($conn, $checkQuery);
	if(mysqli_num_rows($checkResult) > 0) {
		echo "<div class='alert alert-warning text-center mt-4'>⚠️ Type name already exists. Try another.</div>";
	} else {
		$updateQuery = "UPDATE type SET type_name = '$newName' WHERE type_id = '$id'";
		$updateResult = mysqli_query($conn, $updateQuery);

		if(!$updateResult) {
			echo "<div class='alert alert-danger text-center mt-4'>❌ Failed to update type: " . mysqli_error($conn) . "</div>";
		} else {
			echo "<div class='alert alert-success text-center mt-4'>✅ Type updated successfully!</div>";
			header("Refresh:2; url=admin_types.php");
		}
	}
}
?>

<div class="container my-5">
	<div class="card shadow-sm border-0 rounded-4">
		<div class="card-header bg-success text-white text-center">
			<h4 class="mb-0">Edit Medicine Type</h4>
		</div>

		<div class="card-body">
			<form method="post" action="admin_edittypes.php?typeid=<?php echo $row['type_id']; ?>" class="row g-3 justify-content-center">
				
				<input type="hidden" name="id" value="<?php echo $row['type_id']; ?>">

				<div class="col-md-6">
					<label class="form-label">Type Name</label>
					<input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($row['type_name']); ?>" required>
				</div>

				<div class="col-12 text-center mt-4">
					<input type="submit" name="save_change" value="Save Changes" class="btn btn-success px-5">
					<a href="admin_types.php" class="btn btn-outline-secondary ms-2 px-4">Cancel</a>
				</div>
			</form>
		</div>
	</div>
</div>

<?php
if(isset($conn)) { mysqli_close($conn); }
require "./template/footer.php";
?>
