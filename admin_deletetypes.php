<?php
session_start();
if((!isset($_SESSION['manager']) && !isset($_SESSION['expert']))) {
	header("Location:index.php");
	exit;
}

require_once "./functions/database_functions.php";
$conn = db_connect();

if(isset($_GET['typeid'])) {
	$typeid = mysqli_real_escape_string($conn, $_GET['typeid']);

	// Check if type exists
	$checkQuery = "SELECT type_name FROM type WHERE type_id = '$typeid'";
	$checkResult = mysqli_query($conn, $checkQuery);

	if(mysqli_num_rows($checkResult) == 0) {
		echo "<div style='text-align:center; margin-top:50px;' class='alert alert-danger'>
				❌ No type found with ID: $typeid
			  </div>";
		exit;
	} else {
		$row = mysqli_fetch_assoc($checkResult);
		$typeName = $row['type_name'];

		// Check if this type is linked to any medicine
		$checkMed = "SELECT * FROM medicines WHERE type_id = '$typeid'";
		$medResult = mysqli_query($conn, $checkMed);

		if(mysqli_num_rows($medResult) > 0) {
			echo "<div style='text-align:center; margin-top:50px;' class='alert alert-warning'>
					⚠️ Cannot delete type <strong>$typeName</strong> because it is linked to one or more medicines.
				  </div>";
			echo "<meta http-equiv='refresh' content='3;url=admin_types.php'>";
			exit;
		}

		// Delete type
		$query = "DELETE FROM type WHERE type_id = '$typeid'";
		$result = mysqli_query($conn, $query);

		if(!$result) {
			echo "<div style='text-align:center; margin-top:50px;' class='alert alert-danger'>
					❌ Unable to delete type: " . mysqli_error($conn) . "
				  </div>";
			exit;
		} else {
			echo "<div style='text-align:center; margin-top:50px;' class='alert alert-success'>
					✅ Type <strong>$typeName</strong> deleted successfully!
				  </div>";
			header("Refresh:2; url=admin_types.php");
		}
	}
} else {
	echo "<div style='text-align:center; margin-top:50px;' class='alert alert-warning'>
			⚠️ Invalid request. No type selected.
		  </div>";
}

if(isset($conn)) { mysqli_close($conn); }
?>
