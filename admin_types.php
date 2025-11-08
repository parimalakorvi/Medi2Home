<?php
session_start();
if((!isset($_SESSION['manager']) && !isset($_SESSION['expert']))) {
	header("Location:index.php");
	exit;
}

$title = "List of Types";
require_once "./template/header.php";
require_once "./functions/database_functions.php";
$conn = db_connect();
$result = getAlltypes($conn);
?>

<div class="container my-5">
	<div class="d-flex justify-content-between align-items-center mb-3">
		<h3 class="fw-bold text-success">📂 Medicine Types</h3>
		<div>
			<a href="admin_signout.php" class="btn btn-danger me-2">Logout</a>
			<a href="admin_med.php" class="btn btn-outline-success me-2">Medicines</a>
			<a href="admin_used_for.php" class="btn btn-outline-success me-2">Uses</a>
			<?php if(isset($_SESSION['manager']) && $_SESSION['manager'] == true) { ?>
				<a href="admin_addtype.php" class="btn btn-success">➕ Add Type</a>
			<?php } ?>
		</div>
	</div>

	<div class="table-responsive shadow-sm">
		<table class="table table-bordered align-middle">
			<thead class="table-success text-center">
				<tr>
					<th>Type Name</th>
					<th colspan="2">Actions</th>
				</tr>
			</thead>
			<tbody>
				<?php while($row = mysqli_fetch_assoc($result)) { ?>
				<tr>
					<td><?php echo htmlspecialchars($row['type_name']); ?></td>
					<td class="text-center">
						<?php if(isset($_SESSION['expert']) && $_SESSION['expert'] == true) { ?>
							<a href="admin_edittypes.php?typeid=<?php echo $row['type_id']; ?>" 
							   class="btn btn-sm btn-outline-primary">
							   ✏️ Edit
							</a>
						<?php } ?>
						<?php if(isset($_SESSION['manager']) && $_SESSION['manager'] == true) { ?>
							<a href="admin_deletetypes.php?typeid=<?php echo $row['type_id']; ?>" 
							   class="btn btn-sm btn-outline-danger"
							   onclick="return confirm('Are you sure you want to delete this type?');">
							   🗑 Delete
							</a>
						<?php } ?>
					</td>
				</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>

	<?php if(mysqli_num_rows($result) == 0) { ?>
		<div class="alert alert-warning text-center mt-4">
			⚠️ No types found. Add one using the button above.
		</div>
	<?php } ?>
</div>

<?php
if(isset($conn)) { mysqli_close($conn); }
require_once "./template/footer.php";
?>
