<?php
session_start();
include 'db.php';

$message = "";

// Handle Adding New Menu Item
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_item'])) {
    $item_name = $conn->real_escape_string($_POST['item_name']);
    $price = $_POST['price'];

    $insert_sql = "INSERT INTO menu_items (name, price, is_available) VALUES ('$item_name', '$price', 1)";
    if ($conn->query($insert_sql) === TRUE) {
        $message = "<div style='color:green; font-weight:bold; margin-bottom:15px;'>Menu item added successfully!</div>";
    } else {
        $message = "<div style='color:red; font-weight:bold; margin-bottom:15px;'>Error: " . $conn->error . "</div>";
    }
}

// Handle Toggle Availability or Delete
if (isset($_GET['action']) && isset($_GET['id'])) {
    $item_id = $_GET['id'];
    if ($_GET['action'] == 'toggle') {
        $conn->query("UPDATE menu_items SET is_available = NOT is_available WHERE item_id = '$item_id'");
    } elseif ($_GET['action'] == 'delete') {
        $conn->query("DELETE FROM menu_items WHERE item_id = '$item_id'");
    }
    header("Location: add_menu.php");
    exit();
}

// Fetch all menu items
$menu_result = $conn->query("SELECT * FROM menu_items ORDER BY item_id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Manage Canteen Menu</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f9; padding: 20px; }
        .container { max-width: 800px; margin: auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .nav { margin-bottom: 20px; display: flex; justify-content: space-between; }
        .nav a { color: #007bff; text-decoration: none; font-weight: bold; }
        input, button { padding: 10px; margin: 5px 0; width: 100%; box-sizing: border-box; }
        button { background: #28a745; color: white; border: none; font-weight: bold; cursor: pointer; border-radius: 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: center; }
        th { background: #343a40; color: white; }
        .btn-toggle { background: #ffc107; color: #333; padding: 5px 10px; text-decoration: none; border-radius: 4px; font-size: 0.9em; }
        .btn-delete { background: #dc3545; color: white; padding: 5px 10px; text-decoration: none; border-radius: 4px; font-size: 0.9em; }
    </style>
</head>
<body>

<div class="container">
    <div class="nav">
        <a href="staff_orders.php">&larr; Go to Kitchen Orders Dashboard</a>
        <a href="login.php" style="color:#dc3545;">Logout</a>
    </div>

    <h2>Admin Menu Management</h2>
    <?php echo $message; ?>

    <!-- Form to Add Food Item -->
    <form method="POST" action="add_menu.php" style="background:#f9f9f9; padding:15px; border-radius:6px;">
        <h3>Add New Dish</h3>
        <label>Item Name</label>
        <input type="text" name="item_name" required placeholder="e.g., Cold Coffee, Veg Burger">

        <label>Price (Rs.)</label>
        <input type="number" step="0.01" name="price" required placeholder="e.g., 80.00">

        <button type="submit" name="add_item">Add Item to Menu</button>
    </form>

    <!-- Existing Menu Items Table -->
    <h3>Current Menu Items</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Item Name</th>
                <th>Price</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($menu_result->num_rows > 0): ?>
                <?php while($row = $menu_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['item_id']; ?></td>
                        <td><strong><?php echo htmlspecialchars($row['name']); ?></strong></td>
                        <td>Rs. <?php echo number_format($row['price'], 2); ?></td>
                        <td>
                            <?php if ($row['is_available']): ?>
                                <span style="color:green; font-weight:bold;">Available</span>
                            <?php else: ?>
                                <span style="color:red; font-weight:bold;">Out of Stock</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="add_menu.php?action=toggle&id=<?php echo $row['item_id']; ?>" class="btn-toggle">
                                <?php echo $row['is_available'] ? 'Mark Out of Stock' : 'Mark Available'; ?>
                            </a>
                            <a href="add_menu.php?action=delete&id=<?php echo $row['item_id']; ?>" class="btn-delete" onclick="return confirm('Delete this item?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">No menu items found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>