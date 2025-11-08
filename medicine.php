<?php
session_start();
require_once "./functions/database_functions.php";

// ✅ Connect to database
$conn = db_connect();

// ✅ Validate and sanitize medicine serial from URL
if (!isset($_GET['medserial']) || empty($_GET['medserial'])) {
    die("<div class='alert alert-danger text-center mt-5'>⚠️ Invalid medicine selected.</div>");
}
$med_serial = mysqli_real_escape_string($conn, $_GET['medserial']);

// ✅ Fetch medicine details
$query = "
    SELECT m.*, u.used_for_name, t.type_name
    FROM medicines AS m
    LEFT JOIN used_for AS u ON m.used_for_id = u.used_for_id
    LEFT JOIN type AS t ON m.type_id = t.type_id
    WHERE m.med_serial = '$med_serial'
";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) === 0) {
    die("<div class='alert alert-warning text-center mt-5'>❌ Medicine not found.</div>");
}

$row = mysqli_fetch_assoc($result);
$title = $row['med_name'];

require "./template/header.php";
?>

<!-- ===== Breadcrumb ===== -->
<div class="container mt-4">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="index.php">Home</a></li>
      <li class="breadcrumb-item"><a href="medicines.php">Medicines</a></li>
      <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($row['med_name']); ?></li>
    </ol>
  </nav>

  <!-- ===== Medicine Details ===== -->
  <div class="row align-items-start mt-4">
    <div class="col-md-4 text-center mb-4">
      <img src="./bootstrap/img/<?php echo htmlspecialchars($row['med_image']); ?>" 
           alt="<?php echo htmlspecialchars($row['med_name']); ?>" 
           class="img-fluid rounded shadow-sm"
           style="max-height: 300px; object-fit: contain;">
    </div>

    <div class="col-md-8">
      <h2 class="fw-bold text-success"><?php echo htmlspecialchars($row['med_name']); ?></h2>
      <p class="text-muted mb-3"><strong>Manufacturer:</strong> <?php echo htmlspecialchars($row['med_manufacturer']); ?></p>

      <h5>Description</h5>
      <p class="bg-light p-3 rounded"><?php echo nl2br(htmlspecialchars($row['med_descr'])); ?></p>

      <h5 class="mt-4">Details</h5>
      <table class="table table-striped">
        <tr><th>Serial No</th><td><?php echo htmlspecialchars($row['med_serial']); ?></td></tr>
        <tr><th>Price</th><td><strong>₹<?php echo number_format($row['med_price'], 2); ?></strong></td></tr>
        <tr><th>Used For</th><td><?php echo htmlspecialchars($row['used_for_name'] ?? 'Not specified'); ?></td></tr>
        <tr><th>Type</th><td><?php echo htmlspecialchars($row['type_name'] ?? 'Not specified'); ?></td></tr>
      </table>

      <!-- Add to Cart Form -->
      <form method="post" action="cart.php" class="mt-4">
        <input type="hidden" name="medserial" value="<?php echo htmlspecialchars($med_serial); ?>">
        <button type="submit" name="cart" class="btn btn-success btn-lg">
          🛒 Add to Cart
        </button>
      </form>
    </div>
  </div>
</div>

<?php
if (isset($conn)) mysqli_close($conn);
require "./template/footer.php";
?>
