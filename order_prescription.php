<?php
session_start();
require_once "./functions/database_functions.php";
$conn = db_connect();

if (!isset($_SESSION['email'])) {
  header("Location: login.php");
  exit;
}

$email = $_SESSION['email'];
$query = "SELECT id FROM customers WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $email);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$customer_id = $user['id'];

$query = "SELECT * FROM prescriptions WHERE customer_id = ? ORDER BY uploaded_on DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Orders | Medi2Home</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include "./template/header.php"; ?>

<div class="container mt-5">
  <h3 class="text-center text-success mb-4">🧾 My Prescription Orders</h3>

  <?php if (isset($_SESSION['msg'])): ?>
    <div class="alert alert-success text-center"><?php echo $_SESSION['msg']; unset($_SESSION['msg']); ?></div>
  <?php endif; ?>

  <table class="table table-bordered text-center align-middle">
    <thead class="table-success">
      <tr>
        <th>Prescription ID</th>
        <th>File</th>
        <th>Uploaded On</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = $result->fetch_assoc()): ?>
      <tr>
        <td><?php echo $row['prescription_id']; ?></td>
        <td><a href="<?php echo $row['file_name']; ?>" target="_blank">View File</a></td>
        <td><?php echo $row['uploaded_on']; ?></td>
        <td><span class="badge bg-<?php echo ($row['status'] == 'Approved') ? 'success' : (($row['status'] == 'Delivered') ? 'primary' : 'warning'); ?>">
          <?php echo htmlspecialchars($row['status']); ?>
        </span></td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>

<?php include "./template/footer.php"; ?>
</body>
</html>
