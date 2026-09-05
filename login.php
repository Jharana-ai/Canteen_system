<?php
session_start();
include 'db.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role_id'] = $user['role_id'];

            if ($user['role_id'] == 2) {
                header("Location: staff_orders.php");
            } else {
                header("Location: index.php");
            }
            exit();
        } else {
            $message = "<p style='color:red;'>Incorrect password!</p>";
        }
    } else {
        $message = "<p style='color:red;'>No account found with this email!</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Campus Canteen</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f9; padding: 50px; }
        .form-container { background: white; padding: 20px; border-radius: 8px; max-width: 400px; margin: auto; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        input, button { width: 100%; padding: 10px; margin: 10px 0; box-sizing: border-box; }
        button { background: #007bff; color: white; border: none; font-weight: bold; cursor: pointer; }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Canteen Login</h2>
    <?php echo $message; ?>
    <form method="POST" action="login.php">
        <label>Email Address</label>
        <input type="email" name="email" required placeholder="Enter your email">

        <label>Password</label>
        <input type="password" name="password" required placeholder="Enter your password">

        <button type="submit">Login</button>
    </form>
    <p>Don't have an account? <a href="register.php">Register here</a></p>
</div>

</body>
</html>