<?php
// db_connect.php
$servername = "hopper.proxy.rlwy.net";
$username = "root";      // Default XAMPP username
$password = "HEDEOrxqPLaJrHcQbLBtFvJPbcIjZEZA";          // Default XAMPP password is empty
$dbname = "railway";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Connection failed: " . $conn->connect_error]));
}
?>