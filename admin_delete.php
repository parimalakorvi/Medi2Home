<?php
session_start();
if((!isset($_SESSION['manager']) && !isset($_SESSION['expert']))) {
	header("Location:index.php");
	exit;
}

require_once "./functions/database_functions.php";
$conn = db_connect();

if(isset($_GET['medserial'])) {
	$med_serial = mysqli_real_escape_string($conn, $_GET['medserial']);

	// Check if medicine exists
	$checkQuery = "SELECT med_name, med_image FROM medicines WHERE med_serial = '$med_serial'";
	$checkResult = mysqli_query($conn, $checkQuery);

	if(mysqli_num_rows($checkResult) == 0) {
		echo "<div style='text-align:center; margin-top:50px;' class='alert alert-danger'>
				❌ No medicine found with serial: $med_serial
			  </div>";
		exit;
	} else {
		// Optional: delete image file from folder
		$row = mysqli_fetch_assoc($checkResult);
		$imagePath = "./bootstrap/img/" . $row['med_image'];
		if(file_exists($imagePath)) {
			unlink($imagePath);
		}

		// Delete the medicine from DB
		$query = "DELETE FROM medicines WHERE med_serial = '$med_serial'";
		$result = mysqli_query($conn, $query);

		if(!$result) {
			echo "<div style='text-align:center; margin-top:50px;' class='alert alert-danger'>
					❌ Unable to delete medicine: " . mysqli_error($conn) . "
				  </div>";
			exit;
		} else {
			echo "<div style='text-align:center; margin-top:50px;' class='alert alert-success'>
					✅ Medicine deleted successfully!
				  </div>";
			header("Refresh:2; url=admin_med.php");
		}
	}
} else {
	echo "<div style='text-align:center; margin-top:50px;' class='alert alert-warning'>
			⚠️ Invalid request. No medicine selected.
		  </div>";
}

if(isset($conn)) { mysqli_close($conn); }
?>
