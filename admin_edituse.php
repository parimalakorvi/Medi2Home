<?php
session_start();
if((!isset($_SESSION['manager']) && !isset($_SESSION['expert']))) {
	header("Location:index.php");
	exit;
}

$title = "Edit Use";
require_once "./template/header.php";
require_once "./functions/database_functions.php";
$conn = db_connect();

// Check if useforid is provided
if(isset($_GET['useforid'])) {
	$useforid = mysqli_real_escape_string($conn, $_GET['useforid']);
} else {
	echo "<div class='alert alert-danger text-center mt-5'>❌ Invalid request. Missing use ID.</div>";
	exit;
}

// Fetch existing use details
$query = "SELECT * FROM used_for WHERE used_for_id = '$useforid'";
$result = mysqli_query($conn, $query);
if(!$result || mysqli_num_rows($result) == 0) {
	echo "<div class='alert alert-danger text-center mt-5'>❌ Use not found.</div>";
	exit;
}
$row = mysqli_fetch_assoc($result);

// Handle form submission
if(isset($_POST['save_change'])) {
	$newName = trim($_POST['name']);
	$newName = mysqli_real_escape_string($conn, $newName);
	$id = mysqli_real_escape_string($conn, $_POST['id']);

	// Check if name already exists
	$checkQuery = "SELECT * FROM used_for WHERE used_for_name = '$newName' AND used_for_id != '$id'";
	$checkResult = mysqli_query($conn, $checkQuery);
	if(mysqli_num_rows($checkResult) > 0) {
		echo "<div class='alert alert-warning text-center mt-4'>⚠️ This use name already exists. Try another.</div>";
	} else {
		$updateQuery = "UPDATE used_for SET used_for_name = '$newName' WHERE used_for_id = '$id'";
		$updateResult = mysqli_query($conn, $updateQuery);

		if(!$updateResult) {
			echo "<div class='alert alert-danger text-center mt-4'>❌ Failed to update use: " . mysqli_error($conn) . "</div>";
		} else {
			echo "<div class='alert alert-success text-center mt-4'>✅ Use updated successfully!</div>";
			header("Refresh:2; url=admin_used_for.php");
		}
	}
}
?>

<div class="container my-5">
	<div class="card shadow-sm border-0 rounded-4">
		<div class="card-header bg-success text-white text-center">
			<h4 class="mb-0">Edit Medicine Use</h4>
		</div>

		<div class="card-body">
			<form method="post" action="admin_edituse.php?useforid=<?php echo $row['used_for_id']; ?>" class="row g-3 justify-content-center">
				
				<input type="hidden" name="id" value="<?php echo $row['used_for_id']; ?>">

				<div class="col-md-6">
					<label class="form-label">Use Name</label>
					<input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($row['used_for_name']); ?>" required>
				</div>

				<div class="col-12 text-center mt-4">
					<input type="submit" name="save_change" value="Save Changes" class="btn btn-success px-5">
					<a href="admin_used_for.php" class="btn btn-outline-secondary ms-2 px-4">Cancel</a>
				</div>
			</form>
		</div>
	</div>
</div>

<?php
if(isset($conn)) { mysqli_close($conn); }
require "./template/footer.php";
?>
