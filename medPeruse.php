<?php
session_start();
require_once "./functions/database_functions.php";

// ✅ Validate the `useforid`
if (!isset($_GET['useforid']) || !is_numeric($_GET['useforid'])) {
    die("<div class='alert alert-danger text-center mt-5'>⚠️ Invalid request. Please try again.</div>");
}

$useforid = (int) $_GET['useforid'];

// ✅ Connect to database
$conn = db_connect();

// ✅ Get the use-case name
$useName = getuseName($conn, $useforid);

// ✅ Fetch medicines related to this use-case
$query = "
    SELECT med_serial, med_name, med_image, med_price, med_manufacturer 
    FROM medicines 
    WHERE used_for_id = '$useforid'
";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("<div class='alert alert-danger text-center mt-5'>❌ Database error: " . mysqli_error($conn) . "</div>");
}

$title = "Medicines for: " . htmlspecialchars($useName);
require "./template/header.php";
?>

<!-- ===== Breadcrumb ===== -->
<div class="container mt-4">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="index.php">Home</a></li>
      <li class="breadcrumb-item"><a href="medicines.php">Medicines</a></li>
      <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($useName); ?></li>
    </ol>
  </nav>

  <!-- ===== Title ===== -->
  <div class="text-center mb-4">
    <h2 class="fw-bold text-success">💊 Medicines for <?php echo htmlspecialchars($useName); ?></h2>
    <p class="text-muted">Explore all medicines used for this condition or purpose.</p>
  </div>

  <!-- ===== Medicine Grid ===== -->
  <div class="row">
    <?php if (mysqli_num_rows($result) > 0): ?>
      <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <div class="col-md-3 mb-4">
          <div class="card h-100 shadow-sm border-0">
            <img src="./bootstrap/img/<?php echo htmlspecialchars($row['med_image']); ?>"
                 alt="<?php echo htmlspecialchars($row['med_name']); ?>"
                 class="card-img-top p-2"
                 style="height: 200px; object-fit: contain;"
                 onerror="this.src='./bootstrap/img/default-medicine.png';">

            <div class="card-body text-center">
              <h5 class="card-title text-success fw-bold">
                <?php echo htmlspecialchars($row['med_name']); ?>
              </h5>
              <p class="text-muted small"><?php echo htmlspecialchars($row['med_manufacturer']); ?></p>
              <p class="fw-bold mb-2">₹<?php echo number_format($row['med_price'], 2); ?></p>
              <a href="medicine.php?medserial=<?php echo urlencode($row['med_serial']); ?>" 
                 class="btn btn-outline-success btn-sm">
                 View Details
              </a>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <div class="col-12 text-center">
        <div class="alert alert-warning">
          ⚠️ No medicines found for this use case. Please check again later!
        </div>
      </div>
    <?php endif; ?>
  </div>

  <div class="text-center mt-4">
    <a href="medicines.php" class="btn btn-secondary">← Back to All Medicines</a>
  </div>
</div>

<?php
if (isset($conn)) mysqli_close($conn);
require "./template/footer.php";
?>
