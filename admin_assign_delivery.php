<?php
session_start();
require_once "./functions/database_functions.php";
$conn = db_connect();

// ✅ Only admin/manager/expert can access
if (!isset($_SESSION['manager']) && !isset($_SESSION['expert'])) {
  header("Location: login.php");
  exit;
}

// Handle assignment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['agent_id'])) {
  $order_id = intval($_POST['order_id']);
  $agent_id = intval($_POST['agent_id']);

  // Insert assignment record
  $assign = $conn->prepare("INSERT INTO delivery_assignments (order_id, agent_id, delivery_status) VALUES (?, ?, 'Assigned')");
  $assign->bind_param("ii", $order_id, $agent_id);

  if ($assign->execute()) {
    // Update agent status to Busy
    $update_agent = $conn->prepare("UPDATE delivery_agents SET status='Busy' WHERE agent_id=?");
    $update_agent->bind_param("i", $agent_id);
    $update_agent->execute();

    // Update order status
    $update_order = $conn->prepare("UPDATE orders SET status='Packed' WHERE order_id=?");
    $update_order->bind_param("i", $order_id);
    $update_order->execute();

    $msg = "✅ Delivery successfully assigned to agent.";
  } else {
    $msg = "⚠️ Failed to assign delivery.";
  }

  header("Location: admin_assign_delivery.php?msg=" . urlencode($msg));
  exit;
}

// Fetch all approved prescriptions converted into orders (Pending assignment)
$order_query = "SELECT o.order_id, o.customer_email, o.status, p.file_name 
                FROM orders o
                LEFT JOIN prescriptions p ON o.prescription_id = p.prescription_id
                WHERE o.status IN ('Pending', 'Packed')
                ORDER BY o.created_at DESC";
$orders = mysqli_query($conn, $order_query);

// Fetch available delivery agents
$agents_query = "SELECT * FROM delivery_agents WHERE status='Available'";
$agents = mysqli_query($conn, $agents_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Assign Deliveries | Medi2Home</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body { font-family: 'Poppins', sans-serif; background-color: #f8f9fa; }
    .container { margin-top: 50px; }
    .card { border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
    .btn-assign { background-color: #198754; color: #fff; font-weight: 600; }
    .btn-assign:hover { background-color: #146c43; }
  </style>
</head>

<body>
  <?php include "./template/header.php"; ?>

  <div class="container">
    <div class="card p-4">
      <h3 class="text-center text-success mb-3"><i class="bi bi-person-gear"></i> Assign Deliveries</h3>

      <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-info text-center"><?= htmlspecialchars($_GET['msg']); ?></div>
      <?php endif; ?>

      <table class="table table-hover align-middle mt-3">
        <thead class="table-success">
          <tr>
            <th>#</th>
            <th>Order ID</th>
            <th>Customer Email</th>
            <th>Prescription</th>
            <th>Order Status</th>
            <th>Assign to Agent</th>
          </tr>
        </thead>
        <tbody>
          <?php if (mysqli_num_rows($orders) > 0): $i = 1;
            while ($row = mysqli_fetch_assoc($orders)): ?>
              <tr>
                <td><?= $i++; ?></td>
                <td><?= htmlspecialchars($row['order_id']); ?></td>
                <td><?= htmlspecialchars($row['customer_email']); ?></td>
                <td>
                  <?php if (!empty($row['file_name'])): ?>
                    <a href="./uploads/prescriptions/<?= urlencode($row['file_name']); ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                      <i class="bi bi-eye"></i> View
                    </a>
                  <?php else: ?>
                    <span class="text-muted">No file</span>
                  <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($row['status']); ?></td>
                <td>
                  <form method="POST" class="d-flex">
                    <input type="hidden" name="order_id" value="<?= $row['order_id']; ?>">
                    <select name="agent_id" class="form-select form-select-sm me-2" required>
                      <option value="">Select Agent</option>
                      <?php
                        mysqli_data_seek($agents, 0); // Reset pointer
                        while ($agent = mysqli_fetch_assoc($agents)): ?>
                          <option value="<?= $agent['agent_id']; ?>"><?= htmlspecialchars($agent['name']); ?> (<?= htmlspecialchars($agent['status']); ?>)</option>
                      <?php endwhile; ?>
                    </select>
                    <button type="submit" class="btn btn-sm btn-assign"><i class="bi bi-send"></i> Assign</button>
                  </form>
                </td>
              </tr>
            <?php endwhile;
          else: ?>
            <tr><td colspan="6" class="text-center text-muted">No pending orders for delivery assignment.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <?php include "./template/footer.php"; ?>
</body>
</html>
