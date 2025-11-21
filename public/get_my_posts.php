<?php
require 'db_connect.php';
header('Content-Type: application/json');

$user_id = $_GET['user_id'] ?? 0;

$sql = "SELECT posts.id as post_id, posts.image_url, posts.caption, posts.created_at, 
               users.username, users.profile_pic_url 
        FROM posts 
        JOIN users ON posts.user_id = users.user_id 
        WHERE posts.user_id = ? 
        ORDER BY posts.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$posts = array();
while($row = $result->fetch_assoc()) {
    $posts[] = $row;
}

echo json_encode(["status" => "success", "posts" => $posts]);
?>