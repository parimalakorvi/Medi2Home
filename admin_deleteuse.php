<?php
session_start();
if((!isset($_SESSION['manager']) && !isset($_SESSION['expert']))) {
	header("Location:index.php");
	exit;
}

require_once "./functions/database_functions.php";
$conn = db_connect();

if(isset($_GET['useforid'])) {
	$useforid = mysqli_real_escape_string($conn, $_GET['useforid']);

	// Check if use exists
	$checkQuery = "SELECT used_for_name FROM used_for WHERE used_for_id = '$useforid'";
	$checkResult = mysqli_query($conn, $checkQuery);

	if(mysqli_num_rows($checkResult) == 0) {
		echo "<div style='text-align:center; margin-top:50px;' class='alert alert-danger'>
				❌ No use found with ID: $useforid
			  </div>";
		exit;
	} else {
		$row = mysqli_fetch_assoc($checkResult);
		$useName = $row['used_for_name'];

		// Check if this use is linked to any medicine
		$checkMed = "SELECT * FROM medicines WHERE used_for_id = '$useforid'";
		$medResult = mysqli_query($conn, $checkMed);

		if(mysqli_num_rows($medResult) > 0) {
			echo "<div style='text-align:center; margin-top:50px;' class='alert alert-warning'>
					⚠️ Cannot delete use <strong>$useName</strong> because it is linked to one or more medicines.
				  </div>";
			echo "<meta http-equiv='refresh' content='3;url=admin_used_for.php'>";
			exit;
		}

		// Delete the use
		$query = "DELETE FROM used_for WHERE used_for_id = '$useforid'";
		$result = mysqli_query($conn, $query);

		if(!$result) {
			echo "<div style='text-align:center; margin-top:50px;' class='alert alert-danger'>
					❌ Unable to delete use: " . mysqli_error($conn) . "
				  </div>";
			exit;
		} else {
			echo "<div style='text-align:center; margin-top:50px;' class='alert alert-success'>
					✅ Use <strong>$useName</strong> deleted successfully!
				  </div>";
			header("Refresh:2; url=admin_used_for.php");
		}
	}
} else {
	echo "<div style='text-align:center; margin-top:50px;' class='alert alert-warning'>
			⚠️ Invalid request. No use selected.
		  </div>";
}

if(isset($conn)) { mysqli_close($conn); }
?>
