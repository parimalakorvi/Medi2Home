<?php
session_start();
if((!isset($_SESSION['manager']) && !isset($_SESSION['expert']))) {
	header("Location:index.php");
	exit;
}

$title = "List of Uses";
require_once "./template/header.php";
require_once "./functions/database_functions.php";
$conn = db_connect();
$result = getalluse($conn);
?>

<div class="container my-5">
	<div class="d-flex justify-content-between align-items-center mb-3">
		<h3 class="fw-bold text-success">💊 Medicine Uses</h3>
		<div>
			<a href="admin_signout.php" class="btn btn-danger me-2">Logout</a>
			<a href="admin_med.php" class="btn btn-outline-success me-2">Medicines</a>
			<a href="admin_types.php" class="btn btn-outline-success me-2">Types</a>
			<?php if(isset($_SESSION['manager']) && $_SESSION['manager'] == true) { ?>
				<a href="admin_adduse.php" class="btn btn-success">➕ Add Use</a>
			<?php } ?>
		</div>
	</div>

	<div class="table-responsive shadow-sm">
		<table class="table table-bordered align-middle">
			<thead class="table-success text-center">
				<tr>
					<th>Use Name</th>
					<th colspan="2">Actions</th>
				</tr>
			</thead>
			<tbody>
				<?php if(mysqli_num_rows($result) > 0) { ?>
					<?php while($row = mysqli_fetch_assoc($result)) { ?>
					<tr>
						<td><?php echo htmlspecialchars($row['used_for_name']); ?></td>
						<td class="text-center">
							<?php if(isset($_SESSION['expert']) && $_SESSION['expert'] == true) { ?>
								<a href="admin_edituse.php?useforid=<?php echo $row['used_for_id']; ?>" 
								   class="btn btn-sm btn-outline-primary">
								   ✏️ Edit
								</a>
							<?php } ?>
							<?php if(isset($_SESSION['manager']) && $_SESSION['manager'] == true) { ?>
								<a href="admin_deleteuse.php?useforid=<?php echo $row['used_for_id']; ?>" 
								   class="btn btn-sm btn-outline-danger"
								   onclick="return confirm('Are you sure you want to delete this use?');">
								   🗑 Delete
								</a>
							<?php } ?>
						</td>
					</tr>
					<?php } ?>
				<?php } else { ?>
					<tr>
						<td colspan="2" class="text-center text-muted">⚠️ No uses found. Add a new one above.</td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
</div>

<?php
if(isset($conn)) { mysqli_close($conn); }
require_once "./template/footer.php";
?>
