<div style="background: url('https://www.healio.com/~/media/slack-news/stock-images/fm_im/p/pills_shutterstock.jpg') no-repeat center center; background-size: cover; min-height: 100vh;">
<?php
session_start();
require_once "./functions/database_functions.php";
$conn = db_connect();

$query = "SELECT * FROM used_for ORDER BY used_for_name";
$result = mysqli_query($conn, $query);
if (!$result || mysqli_num_rows($result) == 0) {
    die("<div class='alert alert-warning text-center mt-5'>⚠️ No use cases found.</div>");
}

$title = "Medicines by Use";
require "./template/header.php";
?>

<div class="container py-4">
  <h3 class="text-center text-success mb-4">🧾 Browse by Use Case</h3>
  <ul class="list-group shadow-sm">
    <?php while ($row = mysqli_fetch_assoc($result)): ?>
      <?php
        $use_id = $row['used_for_id'];
        $countQuery = "SELECT COUNT(*) AS count FROM medicines WHERE used_for_id = '$use_id'";
        $countResult = mysqli_query($conn, $countQuery);
        $count = mysqli_fetch_assoc($countResult)['count'] ?? 0;
      ?>
      <li class="list-group-item d-flex justify-content-between align-items-center">
        <a href="medPeruse.php?useforid=<?php echo $use_id; ?>" class="text-decoration-none text-dark fw-bold">
          <?php echo htmlspecialchars($row['used_for_name']); ?>
        </a>
        <span class="badge bg-success rounded-pill"><?php echo $count; ?></span>
      </li>
    <?php endwhile; ?>
    <li class="list-group-item text-center">
      <a href="medicines.php" class="btn btn-outline-success btn-sm">View All Medicines</a>
    </li>
  </ul>
</div>

<?php
mysqli_close($conn);
require "./template/footer.php";
?>
</div>
