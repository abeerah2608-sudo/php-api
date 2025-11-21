<?php
require 'db_connect.php';
header('Content-Type: application/json');

$post_id = $_GET['post_id'] ?? 0;

$sql = "SELECT comments.*, users.username, users.profile_pic_url 
        FROM comments 
        JOIN users ON comments.user_id = users.user_id 
        WHERE post_id = $post_id 
        ORDER BY created_at ASC";

$result = $conn->query($sql);
$comments = array();
while($row = $result->fetch_assoc()) {
    $comments[] = $row;
}

echo json_encode(["status" => "success", "comments" => $comments]);
?>