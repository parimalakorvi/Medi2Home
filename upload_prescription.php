<?php
session_start();
require_once "./functions/database_functions.php";
$conn = db_connect();

// Redirect if user not logged in
if (!isset($_SESSION['user']) || !isset($_SESSION['email'])) {
  header("Location: login.php");
  exit;
}

$email = $_SESSION['email'];
$msg = "";

// Handle prescription upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['prescription'])) {
    $file = $_FILES['prescription'];
    $upload_dir = "./uploads/prescriptions/";

    // Create folder if it doesn’t exist
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $file_name = basename($file['name']);
    $target_path = $upload_dir . $file_name;
    $file_type = strtolower(pathinfo($target_path, PATHINFO_EXTENSION));

    // Validate file type
    $allowed = ['jpg', 'jpeg', 'png', 'pdf'];
    if (!in_array($file_type, $allowed)) {
        $msg = "❌ Invalid file type. Only JPG, PNG, or PDF allowed.";
    } elseif (move_uploaded_file($file['tmp_name'], $target_path)) {
        // Save info to database
        $stmt = $conn->prepare("INSERT INTO prescriptions (customer_email, file_name, status) VALUES (?, ?, 'Pending')");
        $stmt->bind_param("ss", $email, $file_name);
        if ($stmt->execute()) {
            $msg = "✅ Prescription uploaded successfully! Waiting for admin approval.";
        } else {
            $msg = "⚠️ Error saving to database. Please try again.";
        }
        $stmt->close();
    } else {
        $msg = "⚠️ Upload failed. Please try again.";
    }
}

// Fetch previous uploads
$query = "SELECT * FROM prescriptions WHERE customer_email = ? ORDER BY uploaded_at DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Upload Prescription | Medi2Home</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body { font-family: 'Poppins', sans-serif; background-color: #f8f9fa; }
    .container { max-width: 800px; margin-top: 50px; }
    .card { border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
    .btn-upload { background-color: #198754; color: #fff; font-weight: 600; }
    .btn-upload:hover { background-color: #146c43; }
    .status-pending { color: orange; font-weight: bold; }
    .status-approved { color: green; font-weight: bold; }
    .status-rejected { color: red; font-weight: bold; }
  </style>
</head>

<body>
  <?php include "./template/header.php"; ?>

  <div class="container">
    <div class="card p-4">
      <h3 class="text-center text-success mb-3"><i class="bi bi-file-earmark-medical"></i> Upload Your Prescription</h3>

      <?php if ($msg): ?>
        <div class="alert alert-info text-center"><?= htmlspecialchars($msg); ?></div>
      <?php endif; ?>

      <form method="POST" enctype="multipart/form-data" class="text-center">
        <div class="mb-3">
          <input type="file" name="prescription" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-upload px-4"><i class="bi bi-cloud-arrow-up"></i> Upload</button>
      </form>
    </div>

    <div class="card mt-5 p-4">
      <h4 class="text-primary"><i class="bi bi-clock-history"></i> Uploaded Prescriptions</h4>
      <table class="table table-hover mt-3">
        <thead class="table-success">
          <tr>
            <th>#</th>
            <th>File Name</th>
            <th>Status</th>
            <th>Uploaded At</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($result->num_rows > 0): 
            $i = 1;
            while ($row = $result->fetch_assoc()): ?>
              <tr>
                <td><?= $i++; ?></td>
                <td><?= htmlspecialchars($row['file_name']); ?></td>
                <td>
                  <?php if ($row['status'] == 'Pending'): ?>
                    <span class="status-pending">Pending</span>
                  <?php elseif ($row['status'] == 'Approved'): ?>
                    <span class="status-approved">Approved</span>
                  <?php else: ?>
                    <span class="status-rejected">Rejected</span>
                  <?php endif; ?>
                </td>
                <td><?= $row['uploaded_at']; ?></td>
                <td>
                  <a href="./uploads/prescriptions/<?= urlencode($row['file_name']); ?>" target="_blank" class="btn btn-sm btn-outline-success">
                    <i class="bi bi-eye"></i> View
                  </a>
                </td>
              </tr>
            <?php endwhile;
          else: ?>
            <tr><td colspan="5" class="text-center text-muted">No prescriptions uploaded yet.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <?php include "./template/footer.php"; ?>
</body>
</html>
