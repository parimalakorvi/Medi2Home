<?php
session_start();
require_once "./functions/database_functions.php";
$conn = db_connect();

// Only allow delivery agents
if (!isset($_SESSION['delivery_agent_email'])) {
  header("Location: login.php");
  exit;
}

$agent_email = $_SESSION['delivery_agent_email'];

// Get agent info
$stmt = $conn->prepare("SELECT * FROM delivery_agents WHERE email = ?");
$stmt->bind_param("s", $agent_email);
$stmt->execute();
$agent = $stmt->get_result()->fetch_assoc();

// Update delivery status if clicked
if (isset($_GET['deliver']) && is_numeric($_GET['deliver'])) {
  $assign_id = $_GET['deliver'];
  $update = $conn->prepare("UPDATE delivery_assignments 
                            SET delivery_status='Delivered', delivered_at=NOW() 
                            WHERE assign_id=? AND agent_id=?");
  $update->bind_param("ii", $assign_id, $agent['agent_id']);
  $update->execute();
}

// Fetch assigned deliveries
$query = "SELECT da.assign_id, o.order_id, o.customer_email, o.status AS order_status,
                 da.delivery_status, da.assigned_at, da.delivered_at
          FROM delivery_assignments da
          JOIN orders o ON da.order_id = o.order_id
          WHERE da.agent_id = ?
          ORDER BY da.assigned_at DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $agent['agent_id']);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Delivery Dashboard | Medi2Home</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body { font-family: 'Poppins', sans-serif; background-color: #f8f9fa; }
    .container { margin-top: 50px; }
    .card { border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
    .status-assigned { color: orange; font-weight: bold; }
    .status-out { color: blue; font-weight: bold; }
    .status-delivered { color: green; font-weight: bold; }
  </style>
</head>

<body>
  <?php include "./template/header.php"; ?>

  <div class="container">
    <div class="card p-4">
      <h3 class="text-center text-success mb-3"><i class="bi bi-truck"></i> Delivery Dashboard</h3>
      <p class="text-center text-muted">Welcome, <strong><?= htmlspecialchars($agent['name']); ?></strong> | Status: <?= htmlspecialchars($agent['status']); ?></p>

      <table class="table table-hover mt-3 align-middle">
        <thead class="table-success">
          <tr>
            <th>#</th>
            <th>Order ID</th>
            <th>Customer Email</th>
            <th>Order Status</th>
            <th>Delivery Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($result->num_rows > 0): $i = 1;
            while ($row = $result->fetch_assoc()): ?>
              <tr>
                <td><?= $i++; ?></td>
                <td><?= htmlspecialchars($row['order_id']); ?></td>
                <td><?= htmlspecialchars($row['customer_email']); ?></td>
                <td><?= htmlspecialchars($row['order_status']); ?></td>
                <td>
                  <?php if ($row['delivery_status'] == 'Assigned'): ?>
                    <span class="status-assigned">Assigned</span>
                  <?php elseif ($row['delivery_status'] == 'Out for Delivery'): ?>
                    <span class="status-out">Out for Delivery</span>
                  <?php else: ?>
                    <span class="status-delivered">Delivered</span>
                  <?php endif; ?>
                </td>
                <td>
                  <?php if ($row['delivery_status'] != 'Delivered'): ?>
                    <a href="?deliver=<?= $row['assign_id']; ?>" class="btn btn-sm btn-success">
                      <i class="bi bi-check-circle"></i> Mark Delivered
                    </a>
                  <?php else: ?>
                    <span class="text-muted">✅ Done</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endwhile;
          else: ?>
            <tr><td colspan="6" class="text-center text-muted">No assigned deliveries yet.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <?php include "./template/footer.php"; ?>
</body>
</html>
