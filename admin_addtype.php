<?php
session_start();
if((!isset($_SESSION['manager']) && !isset($_SESSION['expert']))) {
	header("Location:index.php");
	exit;
}

$title = "Add New Type";
require "./template/header.php";
require "./functions/database_functions.php";
$conn = db_connect();

$message = "";

if(isset($_POST['add'])) {
	$name = trim($_POST['name']);
	$name = mysqli_real_escape_string($conn, $name);

	if($name == "") {
		$message = "<div class='alert alert-warning text-center'>⚠️ Please enter a type name.</div>";
	} else {
		$findtype = "SELECT * FROM type WHERE type_name = '$name'";
		$findResult = mysqli_query($conn, $findtype);

		if(mysqli_num_rows($findResult) == 0) {
			$inserttype = "INSERT INTO type(type_name) VALUES ('$name')";
			$insertResult = mysqli_query($conn, $inserttype);

			if(!$insertResult) {
				$message = "<div class='alert alert-danger text-center'>❌ Can't add new type: " . mysqli_error($conn) . "</div>";
			} else {
				$message = "<div class='alert alert-success text-center'>✅ Type added successfully!</div>";
				header("Refresh:2; url=admin_types.php");
			}
		} else {
			$message = "<div class='alert alert-danger text-center'>⚠️ Type already exists!</div>";
		}
	}
}
?>

<div class="container my-5">
	<div class="card shadow-sm border-0 rounded-4">
		<div class="card-header bg-success text-white text-center">
			<h4 class="mb-0">Add New Medicine Type</h4>
		</div>

		<div class="card-body">
			<?php if($message != "") echo $message; ?>

			<form method="post" action="admin_addtype.php" enctype="multipart/form-data" class="row g-3 justify-content-center">
				<div class="col-md-6">
					<label class="form-label">Type Name</label>
					<input type="text" name="name" class="form-control" placeholder="e.g., Tablet, Syrup, Capsule" required>
				</div>

				<div class="col-12 text-center mt-4">
					<input type="submit" name="add" value="Add Type" class="btn btn-success px-5">
					<a href="admin_types.php" class="btn btn-outline-secondary ms-2 px-4">Cancel</a>
				</div>
			</form>
		</div>
	</div>
</div>

<?php
if(isset($conn)) { mysqli_close($conn); }
require_once "./template/footer.php";
?>
