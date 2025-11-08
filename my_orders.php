<?php
session_start();
require_once "./functions/database_functions.php";
$conn = db_connect();

// ✅ Only logged-in users can view
if (!isset($_SESSION['user']) || !isset($_SESSION['email'])) {
  header("Location: login.php");
  exit;
}

$user_email = $_SESSION['email'];

// Fetch user orders and prescription details
$query = "
  SELECT 
    o.order_id, o.status AS order_status, o.total_price, o.created_at,
    p.prescription_id, p.file_name, p.status AS prescription_status,
    da.delivery_status, da.assigned_at, da.delivered_at,
    ag.name AS agent_name
  FROM orders o
  LEFT JOIN prescriptions p ON o.prescription_id = p.prescription_id
  LEFT JOIN delivery_assignments da ON o.order_id = da.order_id
  LEFT JOIN delivery_agents ag ON da.agent_id = ag.agent_id
  WHERE o.customer_email = ?
  ORDER BY o.created_at DESC
";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>My Orders | Medi2Home</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body { font-family: 'Poppins', sans-serif; background-color: #f8f9fa; }
    .container { margin-top: 50px; }
    .card { border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
    .status-pending { color: orange; font-weight: bold; }
    .status-packed { color: #0d6efd; font-weight: bold; }
    .status-delivered { color: green; font-weight: bold; }
    .status-rejected { color: red; font-weight: bold; }
  </style>
</head>

<body>
  <?php include "./template/header.php"; ?>

  <div class="container">
    <div class="card p-4">
      <h3 class="text-center text-success mb-3"><i class="bi bi-bag-check"></i> My Orders</h3>
      <p class="text-center text-muted mb-4">Track your prescriptions and medicine delivery status easily.</p>

      <table class="table table-hover align-middle">
        <thead class="table-success">
          <tr>
            <th>#</th>
            <th>Prescription</th>
            <th>Prescription Status</th>
            <th>Order Status</th>
            <th>Delivery Agent</th>
            <th>Delivery Status</th>
            <th>Ordered On</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($result->num_rows > 0): $i = 1;
            while ($row = $result->fetch_assoc()): ?>
              <tr>
                <td><?= $i++; ?></td>
                <td>
                  <?php if (!empty($row['file_name'])): ?>
                    <a href="./uploads/prescriptions/<?= urlencode($row['file_name']); ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                      <i class="bi bi-eye"></i> View
                    </a>
                  <?php else: ?>
                    <span class="text-muted">—</span>
                  <?php endif; ?>
                </td>
                <td>
                  <?php if ($row['prescription_status'] == 'Pending'): ?>
                    <span class="status-pending">Pending</span>
                  <?php elseif ($row['prescription_status'] == 'Approved'): ?>
                    <span class="status-packed">Approved</span>
                  <?php elseif ($row['prescription_status'] == 'Rejected'): ?>
                    <span class="status-rejected">Rejected</span>
                  <?php endif; ?>
                </td>
                <td>
                  <?php
                    switch ($row['order_status']) {
                      case 'Pending': echo '<span class="status-pending">Pending</span>'; break;
                      case 'Packed': echo '<span class="status-packed">Packed</span>'; break;
                      case 'Delivered': echo '<span class="status-delivered">Delivered</span>'; break;
                      case 'Cancelled': echo '<span class="status-rejected">Cancelled</span>'; break;
                    }
                  ?>
                </td>
                <td><?= $row['agent_name'] ? htmlspecialchars($row['agent_name']) : '<span class="text-muted">Not Assigned</span>'; ?></td>
                <td>
                  <?php
                    if ($row['delivery_status'] == 'Delivered') echo '<span class="status-delivered">Delivered</span>';
                    elseif ($row['delivery_status'] == 'Out for Delivery') echo '<span class="status-packed">Out for Delivery</span>';
                    elseif ($row['delivery_status'] == 'Assigned') echo '<span class="status-pending">Assigned</span>';
                    else echo '<span class="text-muted">—</span>';
                  ?>
                </td>
                <td><?= date("d M Y, h:i A", strtotime($row['created_at'])); ?></td>
              </tr>
            <?php endwhile;
          else: ?>
            <tr><td colspan="7" class="text-center text-muted">You have not placed any orders yet.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <?php include "./template/footer.php"; ?>
</body>
</html>
