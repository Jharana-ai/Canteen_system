<?php
include 'db.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role_id = $_POST['role_id'];

    // Insert query
    $sql = "INSERT INTO users (full_name, email, password_hash, role_id) VALUES ('$full_name', '$email', '$password', '$role_id')";

    if ($conn->query($sql) === TRUE) {
        $message = "<p style='color:green;'>Registration successful! You can now <a href='login.php'>Login</a></p>";
    } else {
        $message = "<p style='color:red;'>Error: " . $conn->error . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - Campus Canteen</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f9; padding: 50px; }
        .form-container { background: white; padding: 20px; border-radius: 8px; max-width: 400px; margin: auto; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        input, select, button { width: 100%; padding: 10px; margin: 10px 0; box-sizing: border-box; }
        button { background: #28a745; color: white; border: none; font-weight: bold; cursor: pointer; }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Student / Staff Register</h2>
    <?php echo $message; ?>
    <form method="POST" action="register.php">
        <label>Full Name</label>
        <input type="text" name="full_name" required placeholder="Enter your full name">

        <label>Email Address</label>
        <input type="email" name="email" required placeholder="Enter your email">

        <label>Password</label>
        <input type="password" name="password" required placeholder="Create a password">

        <label>Role</label>
        <select name="role_id">
            <option value="1">Student</option>
            <option value="2">Canteen Staff</option>
        </select>

        <button type="submit">Register Account</button>
    </form>
</div>

</body>
</html>