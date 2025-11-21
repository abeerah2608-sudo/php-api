<?php
require 'db_connect.php';
header('Content-Type: application/json');

$user_id = $_POST['user_id'] ?? '';
$post_id = $_POST['post_id'] ?? '';
$text = $_POST['comment_text'] ?? '';

if (!empty($user_id) && !empty($post_id) && !empty($text)) {
    $stmt = $conn->prepare("INSERT INTO comments (post_id, user_id, comment_text) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $post_id, $user_id, $text);
    
    if ($stmt->execute()) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Missing data"]);
}
?>