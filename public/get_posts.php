<?php
require 'db_connect.php';
header('Content-Type: application/json');

// Updated Query: Fetches Post, User, AND Comment Count
$sql = "SELECT posts.post_id as post_id, posts.image_url, posts.caption, posts.created_at, 
               users.username, users.profile_pic_url,
               (SELECT COUNT(*) FROM comments WHERE comments.post_id = posts.post_id) as comments_count
        FROM posts 
        JOIN users ON posts.user_id = users.user_id 
        ORDER BY posts.created_at DESC";


$result = $conn->query($sql);

$posts = array();
while($row = $result->fetch_assoc()) {
    $posts[] = $row;
}

echo json_encode(["status" => "success", "posts" => $posts]);
$conn->close();

?>


