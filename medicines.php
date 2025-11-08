<?php
session_start();
require_once "./functions/database_functions.php";
$conn = db_connect();

$title = "All Medicines";
require_once "./template/header.php";

// ===== Initialize Sorting and Searching =====
$orderBy = "med_name";
$orderType = "ASC";

if (isset($_POST['sort_by'])) {
    $orderBy = $_POST['sort_by'];
}
if (isset($_POST['order']) && in_array($_POST['order'], ['ASC', 'DESC'])) {
    $orderType = $_POST['order'];
}

$search = "";
if (isset($_POST['search']) && !empty(trim($_POST['search']))) {
    $search = mysqli_real_escape_string($conn, trim($_POST['search']));
}

// ===== Build Query =====
$query = "SELECT * FROM medicines";
if ($search) {
    $query .= " WHERE med_name LIKE '%$search%' OR med_manufacturer LIKE '%$search%'";
}
$query .= " ORDER BY $orderBy $orderType";

$result = mysqli_query($conn, $query);
if (!$result) {
    die("Database query failed: " . mysqli_error($conn));
}
?>

<!-- ===== Page Header ===== -->
<div class="text-center my-4">
  <h3 class="fw-bold text-success">💊 All Available Medicines</h3>
  <p class="text-muted">Browse, search, and sort medicines by name, price, or manufacturer.</p>
</div>

<!-- ===== Search & Sort Form ===== -->
<form method="post" action="medicines.php" class="d-flex flex-wrap justify-content-center align-items-center gap-2 mb-4">
  <input type="text" name="search" class="form-control w-auto" placeholder="🔍 Search medicines..." value="<?php echo htmlspecialchars($search); ?>">

  <select name="sort_by" class="form-select w-auto">
    <option value="med_name" <?php if($orderBy=='med_name') echo 'selected'; ?>>Name</option>
    <option value="med_price" <?php if($orderBy=='med_price') echo 'selected'; ?>>Price</option>
    <option value="med_manufacturer" <?php if($orderBy=='med_manufacturer') echo 'selected'; ?>>Manufacturer</option>
  </select>

  <select name="order" class="form-select w-auto">
    <option value="ASC" <?php if($orderType=='ASC') echo 'selected'; ?>>Ascending</option>
    <option value="DESC" <?php if($orderType=='DESC') echo 'selected'; ?>>Descending</option>
  </select>

  <button type="submit" class="btn btn-success">Sort</button>
  <a href="medicines.php" class="btn btn-secondary">Clear</a>
</form>

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
            <h5 class="card-title text-success fw-bold"><?php echo htmlspecialchars($row['med_name']); ?></h5>
            <p class="text-muted small"><?php echo htmlspecialchars($row['med_manufacturer']); ?></p>
            <p class="fw-bold">₹<?php echo number_format($row['med_price'], 2); ?></p>
            <a href="medicine.php?medserial=<?php echo urlencode($row['med_serial']); ?>" class="btn btn-outline-success btn-sm">
              View Details
            </a>
          </div>
        </div>
      </div>
    <?php endwhile; ?>
  <?php else: ?>
    <div class="col-12 text-center mt-4">
      <div class="alert alert-warning">⚠️ No medicines found for your search.</div>
    </div>
  <?php endif; ?>
</div>

<?php
if (isset($conn)) { mysqli_close($conn); }
require_once "./template/footer.php";
?>
