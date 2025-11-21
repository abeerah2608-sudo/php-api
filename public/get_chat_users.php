<?php
require 'db_connect.php';
header('Content-Type: application/json');

$my_id = $_GET['user_id'] ?? 0;

// Get all users EXCEPT me (so I don't chat with myself)
$sql = "SELECT user_id, username, profile_pic_url, is_online FROM users WHERE user_id != ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $my_id);
$stmt->execute();
$result = $stmt->get_result();

$users = array();
while($row = $result->fetch_assoc()) {
    $users[] = $row;
}

echo json_encode(["status" => "success", "users" => $users]);
?>