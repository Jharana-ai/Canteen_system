<?php
session_start();
include 'db.php';

// Redirect to login if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$message = "";

// Handle Order Placement
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['place_order'])) {
    $user_id = $_SESSION['user_id'];
    $item_id = $_POST['item_id'];
    $price = $_POST['price'];

    // Insert order record
    $order_sql = "INSERT INTO orders (user_id, total_amount, status) VALUES ('$user_id', '$price', 'Received')";
    if ($conn->query($order_sql) === TRUE) {
        $order_id = $conn->insert_id;
        
        // Generate unique token string (e.g., CAN-1042)
        $token_number = "CAN-" . rand(1000, 9999);
        $token_sql = "INSERT INTO tokens (order_id, token_number) VALUES ('$order_id', '$token_number')";
        $conn->query($token_sql);

        $message = "<div style='background:#d4edda; color:#155724; padding:10px; margin-bottom:15px; border-radius:4px;'>Order Placed Successfully! Your Digital Token is: <strong>$token_number</strong></div>";
    } else {
        $message = "<div style='background:#f8d7da; color:#721c24; padding:10px; margin-bottom:15px; border-radius:4px;'>Failed to place order: " . $conn->error . "</div>";
    }
}

// Fetch menu items from database
$menu_result = $conn->query("SELECT * FROM menu_items WHERE is_available = 1");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Campus Canteen - Daily Menu</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f9; margin: 0; padding: 20px; }
        .header { display: flex; justify-content: space-between; align-items: center; background: white; padding: 15px 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .container { max-width: 900px; margin: 20px auto; }
        .menu-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px; }
        .menu-card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); text-align: center; }
        .price { font-size: 1.2em; color: #28a745; font-weight: bold; margin: 10px 0; }
        .order-btn { background: #007bff; color: white; border: none; padding: 10px 15px; border-radius: 4px; cursor: pointer; font-weight: bold; width: 100%; }
        .logout-btn { color: #dc3545; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?>!</h2>
        <div>
            <a href="my_orders.php" style="margin-right: 15px; font-weight:bold; text-decoration:none; color:#007bff;">My Orders</a>
            <a href="login.php" class="logout-btn">Logout</a>
        </div>
    </div>

    <h3>Today's Fresh Canteen Menu</h3>
    <?php echo $message; ?>

    <div class="menu-grid">
        <?php if ($menu_result->num_rows > 0): ?>
            <?php while($item = $menu_result->fetch_assoc()): ?>
                <div class="menu-card">
                    <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                    <div class="price">Rs. <?php echo number_format($item['price'], 2); ?></div>
                    <form method="POST" action="index.php">
                        <input type="hidden" name="item_id" value="<?php echo $item['item_id']; ?>">
                        <input type="hidden" name="price" value="<?php echo $item['price']; ?>">
                        <button type="submit" name="place_order" class="order-btn">Pre-Order Now</button>
                    </form>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No menu items available today.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>