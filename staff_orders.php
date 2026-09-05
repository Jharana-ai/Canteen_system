<?php
session_start();
include 'db.php';

// Handle Status Updates
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['status'];
    
    $update_sql = "UPDATE orders SET status = '$new_status' WHERE order_id = '$order_id'";
    $conn->query($update_sql);
}

// Fetch all orders with token number and user info
$orders_sql = "SELECT orders.order_id, orders.total_amount, orders.status, orders.placed_at, 
                      users.full_name, tokens.token_number 
               FROM orders 
               JOIN users ON orders.user_id = users.user_id 
               LEFT JOIN tokens ON orders.order_id = tokens.order_id 
               ORDER BY orders.placed_at DESC";

$orders_result = $conn->query($orders_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <!-- Auto-refresh the page every 10 seconds for live order tracking -->
    <meta http-equiv="refresh" content="10">
    <title>Kitchen Staff - Live Order Queue</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f9; padding: 20px; }
        .container { max-width: 1000px; margin: auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .nav { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 10px; }
        .btn-menu { background: #28a745; color: white; text-decoration: none; padding: 8px 14px; border-radius: 4px; font-weight: bold; }
        .btn-logout { color: #dc3545; text-decoration: none; font-weight: bold; margin-left: 15px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: center; }
        th { background: #343a40; color: white; }
        .badge { padding: 6px 12px; border-radius: 4px; font-weight: bold; color: white; display: inline-block; }
        .Received { background: #ffc107; color: #333; }
        .Preparing { background: #17a2b8; }
        .Ready { background: #28a745; }
        select, button { padding: 6px 10px; cursor: pointer; }
        button { background: #007bff; color: white; border: none; border-radius: 4px; font-weight: bold; }
    </style>
</head>
<body>

<div class="container">
    <!-- Header Navigation Bar -->
    <div class="nav">
        <div>
            <a href="add_menu.php" class="btn-menu">+ Manage Menu Items</a>
        </div>
        <div>
            <a href="login.php" class="btn-logout">Logout</a>
        </div>
    </div>

    <h2>Kitchen Live Order Queue</h2>
    <p>View incoming orders and update food preparation status in real-time. <em>(Auto-refreshes every 10s)</em></p>

    <table>
        <thead>
            <tr>
                <th>Token #</th>
                <th>Customer</th>
                <th>Amount</th>
                <th>Current Status</th>
                <th>Placed At</th>
                <th>Update Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($orders_result && $orders_result->num_rows > 0): ?>
                <?php while($row = $orders_result->fetch_assoc()): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($row['token_number'] ?? 'N/A'); ?></strong></td>
                        <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                        <td>Rs. <?php echo number_format($row['total_amount'], 2); ?></td>
                        <td><span class="badge <?php echo $row['status']; ?>"><?php echo $row['status']; ?></span></td>
                        <td><?php echo date('h:i A', strtotime($row['placed_at'])); ?></td>
                        <td>
                            <form method="POST" action="staff_orders.php" style="display:inline;">
                                <input type="hidden" name="order_id" value="<?php echo $row['order_id']; ?>">
                                <select name="status">
                                    <option value="Received" <?php if($row['status']=='Received') echo 'selected'; ?>>Received</option>
                                    <option value="Preparing" <?php if($row['status']=='Preparing') echo 'selected'; ?>>Preparing</option>
                                    <option value="Ready" <?php if($row['status']=='Ready') echo 'selected'; ?>>Ready</option>
                                </select>
                                <button type="submit" name="update_status">Save</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">No orders placed yet.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>