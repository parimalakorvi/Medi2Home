<?php
$title = "Search Results";
require_once "./functions/database_functions.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['text'])) {
    $text = trim($_POST['text']);
} else {
    die("<div class='alert alert-warning text-center mt-5'>⚠️ Invalid search request.</div>");
}

$conn = db_connect();

// ✅ Prevent SQL injection
$text = mysqli_real_escape_string($conn, $text);

$query = "
  SELECT * FROM medicines 
  JOIN used_for ON medicines.used_for_id = used_for.used_for_id 
  WHERE med_serial LIKE '%$text%' 
  OR med_manufacturer LIKE '%$text%' 
  OR med_name LIKE '%$text%' 
  OR used_for_name LIKE '%$text%'
";

$result = mysqli_query($conn, $query);
$count = mysqli_num_rows($result);

require_once "./template/header.php";
?>

<div class="container mt-4">
  <h3 class="text-center text-success mb-3">🔍 Search Results for "<em><?php echo htmlspecialchars($text); ?></em>"</h3>

  <?php if ($count === 0): ?>
    <div class="alert alert-warning text-center">No medicines found. Try another keyword!</div>
  <?php else: ?>
    <div class="alert alert-success text-center"><?php echo $count; ?> medicines found</div>

    <div class="row">
      <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <div class="col-md-3 mb-4">
          <div class="card shadow-sm border-0 h-100">
            <a href="medicine.php?medserial=<?php echo urlencode($row['med_serial']); ?>">
              <img src="./bootstrap/img/<?php echo htmlspecialchars($row['med_image']); ?>" 
                   class="card-img-top p-2" 
                   style="height: 200px; object-fit: contain;"
                   onerror="this.src='./bootstrap/img/default-medicine.png';">
            </a>
            <div class="card-body text-center">
              <h6 class="fw-bold text-success"><?php echo htmlspecialchars($row['med_name']); ?></h6>
              <p class="text-muted small mb-0"><?php echo htmlspecialchars($row['med_manufacturer']); ?></p>
              <p class="text-primary fw-bold mt-2">₹<?php echo number_format($row['med_price'], 2); ?></p>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    </div>
  <?php endif; ?>
</div>

<?php
if (isset($conn)) mysqli_close($conn);
require_once "./template/footer.php";
?>
