<?php
require 'db_connect.php';
header('Content-Type: application/json');

// Select stories created in the last 24 hours with user's username and profile picture
$sql = "SELECT s.*, u.username, u.profile_pic_url 
        FROM stories s
        JOIN users u ON s.user_id = u.user_id
        WHERE s.created_at >= DATE_SUB(NOW(), INTERVAL 1 DAY)
        ORDER BY s.created_at DESC";

$result = $conn->query($sql);

$stories = array();
while($row = $result->fetch_assoc()) {
    $stories[] = $row;
}

echo json_encode(["status" => "success", "stories" => $stories]);
$conn->close();
?>
