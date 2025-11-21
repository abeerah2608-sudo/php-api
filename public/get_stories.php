<?php
require 'db_connect.php';
header('Content-Type: application/json');

// Logic: Select stories created in the last 24 hours
// We JOIN with 'users' table to get the username and profile pic of the story poster
$sql = "SELECT stories.*, users.username, users.profile_pic_url 
        FROM stories 
        JOIN users ON stories.user_id = users.user_id 
        WHERE stories.created_at >= DATE_SUB(NOW(), INTERVAL 1 DAY) 
        ORDER BY stories.created_at DESC";

$result = $conn->query($sql);

$stories = array();
while($row = $result->fetch_assoc()) {
    $stories[] = $row;
}

echo json_encode(["status" => "success", "stories" => $stories]);
$conn->close();
?>