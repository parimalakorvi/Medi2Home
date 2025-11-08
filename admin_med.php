<?php
session_start();
if((!isset($_SESSION['manager']) && !isset($_SESSION['expert']))) {
	header("Location:index.php");
	exit;
}

$title = "Medicine Management";
require_once "./template/header.php";
require_once "./functions/database_functions.php";
$conn = db_connect();
$result = getAll($conn);
?>

<div class="container my-5">
	<div class="d-flex justify-content-between align-items-center mb-3">
		<h3 class="fw-bold text-success">📋 List of Medicines</h3>
		<div>
			<a href="admin_signout.php" class="btn btn-danger me-2">Logout</a>
			<a href="admin_used_for.php" class="btn btn-outline-success me-2">Manage Uses</a>
			<a href="admin_types.php" class="btn btn-outline-success me-2">Manage Types</a>
			<?php if(isset($_SESSION['manager']) && $_SESSION['manager'] == true) { ?>
				<a href="admin_add.php" class="btn btn-success">➕ Add New Medicine</a>
			<?php } ?>
		</div>
	</div>

	<div class="table-responsive shadow-sm">
		<table class="table table-bordered align-middle">
			<thead class="table-success text-center">
				<tr>
					<th>Serial No</th>
					<th>Image</th>
					<th>Medicine Name</th>
					<th>Manufacturer</th>
					<th>Description</th>
					<th>Price (₹)</th>
					<th>Used For</th>
					<th>Type</th>
					<th colspan="2">Actions</th>
				</tr>
			</thead>
			<tbody>
				<?php while($row = mysqli_fetch_assoc($result)) { ?>
				<tr>
					<td class="text-center"><?php echo htmlspecialchars($row['med_serial']); ?></td>
					<td class="text-center">
						<?php if($row['med_image']) { ?>
							<img src="./bootstrap/img/<?php echo htmlspecialchars($row['med_image']); ?>" 
								 alt="Medicine Image" width="60" height="60" class="img-thumbnail rounded">
						<?php } else { ?>
							<span class="text-muted">No Image</span>
						<?php } ?>
					</td>
					<td><?php echo htmlspecialchars($row['med_name']); ?></td>
					<td><?php echo htmlspecialchars($row['med_manufacturer']); ?></td>
					<td><?php echo nl2br(htmlspecialchars($row['med_descr'])); ?></td>
					<td class="text-center fw-bold">₹<?php echo number_format($row['med_price'], 2); ?></td>
					<td><?php echo htmlspecialchars(getuseName($conn, $row['used_for_id'])); ?></td>
					<td><?php echo htmlspecialchars(gettypeName($conn, $row['type_id'])); ?></td>
					
					<td class="text-center">
						<?php if(isset($_SESSION['expert']) && $_SESSION['expert'] == true) { ?>
							<a href="admin_edit.php?medserial=<?php echo $row['med_serial']; ?>" 
							   class="btn btn-sm btn-outline-primary">✏️ Edit</a>
						<?php } ?>
						<?php if(isset($_SESSION['manager']) && $_SESSION['manager'] == true) { ?>
							<a href="admin_delete.php?medserial=<?php echo $row['med_serial']; ?>" 
							   class="btn btn-sm btn-outline-danger"
							   onclick="return confirm('Are you sure you want to delete this medicine?');">
							   🗑 Delete
							</a>
						<?php } ?>
					</td>
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
