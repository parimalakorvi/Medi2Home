<?php
session_start();
require_once "./functions/database_functions.php";

// ✅ Validate type ID
if (!isset($_GET['typeid']) || !is_numeric($_GET['typeid'])) {
    die("<div class='alert alert-danger text-center mt-5'>⚠️ Invalid request. Please try again.</div>");
}

$typeid = (int) $_GET['typeid'];

// ✅ Connect to DB
$conn = db_connect();
$typename = gettypeName($conn, $typeid);

// ✅ Fetch all medicines for this type
$query = "
    SELECT med_serial, med_name, med_image, med_price, med_manufacturer 
    FROM medicines 
    WHERE type_id = '$typeid'
";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("<div class='alert alert-danger text-center mt-5'>❌ Error fetching data: " . mysqli_error($conn) . "</div>");
}

$title = "Medicines of Type: " . $typename;
require "./template/header.php";
?>

<!-- ===== Breadcrumb ===== -->
<div class="container mt-4">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="index.php">Home</a></li>
      <li class="breadcrumb-item"><a href="medicines.php">Medicines</a></li>
      <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($typename); ?></li>
    </ol>
  </nav>

  <!-- ===== Page Title ===== -->
  <div class="text-center mb-4">
    <h2 class="fw-bold text-success">💊 <?php echo htmlspecialchars($typename); ?> Medicines</h2>
    <p class="text-muted">Explore all medicines belonging to this category.</p>
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
              <p class="text-muted small mb-1">
                <?php echo htmlspecialchars($row['med_manufacturer']); ?>
              </p>
              <p class="fw-bold mb-3">₹<?php echo number_format($row['med_price'], 2); ?></p>
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
          ⚠️ No medicines found for this category. Please check back later!
        </div>
      </div>
    <?php endif; ?>
  </div>

  <!-- ===== Back Button ===== -->
  <div class="text-center mt-4">
    <a href="medicines.php" class="btn btn-secondary">← Back to All Medicines</a>
  </div>
</div>

<?php
if (isset($conn)) mysqli_close($conn);
require "./template/footer.php";
?>
