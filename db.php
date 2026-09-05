<?php
$host = "127.0.0.1";
$user = "root";
$password = "";
$database = "canteen_db";
$port = 3307; // Matches your XAMPP MySQL port

$conn = new mysqli($host, $user, $password, $database, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>