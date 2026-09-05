<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user's order history with token and status
$sql = "SELECT orders.order_id, orders.total_amount, orders.status, orders.placed_at, tokens.token_number 
        FROM orders 
        LEFT JOIN tokens ON orders.order_id = tokens.order_id 
        WHERE orders.user_id = '$user_id' 
        ORDER BY orders.placed_at DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Orders - Campus Canteen</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f9; padding: 20px; }
        .container { max-width: 800px; margin: auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .nav { margin-bottom: 20px; display: flex; justify-content: space-between; }
        .nav a { color: #007bff; text-decoration: none; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: center; }
        th { background: #007bff; color: white; }
        .badge { padding: 6px 12px; border-radius: 4px; font-weight: bold; color: white; display: inline-block; }
        .Received { background: #ffc107; color: #333; }
        .Preparing { background: #17a2b8; }
        .Ready { background: #28a745; }
    </style>
</head>
<body>

<div class="container">
    <div class="nav">
        <a href="index.php">&larr; Back to Food Menu</a>
        <a href="login.php" style="color:#dc3545;">Logout</a>
    </div>

    <h2>My Order History & Digital Tokens</h2>

    <table>
        <thead>
            <tr>
                <th>Token #</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Time Placed</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($row['token_number']); ?></strong></td>
                        <td>Rs. <?php echo number_format($row['total_amount'], 2); ?></td>
                        <td><span class="badge <?php echo $row['status']; ?>"><?php echo $row['status']; ?></span></td>
                        <td><?php echo date('h:i A', strtotime($row['placed_at'])); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">No orders placed yet.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>