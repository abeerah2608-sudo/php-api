<?php
require 'db_connect.php';
header('Content-Type: application/json');

$user_id = $_POST['user_id'] ?? '';
$post_id = $_POST['post_id'] ?? '';

if (!empty($user_id) && !empty($post_id)) {
    // Check if already liked
    $check = $conn->prepare("SELECT like_id FROM likes WHERE user_id = ? AND post_id = ?");
    $check->bind_param("ii", $user_id, $post_id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        // Already liked -> Unlike (Delete)
        $del = $conn->prepare("DELETE FROM likes WHERE user_id = ? AND post_id = ?");
        $del->bind_param("ii", $user_id, $post_id);
        $del->execute();
        $action = "unliked";
    } else {
        // Not liked -> Like (Insert)
        $ins = $conn->prepare("INSERT INTO likes (user_id, post_id) VALUES (?, ?)");
        $ins->bind_param("ii", $user_id, $post_id);
        $ins->execute();
        $action = "liked";
    }

    // Get new like count
    $countQuery = $conn->query("SELECT COUNT(*) as c FROM likes WHERE post_id = $post_id");
    $row = $countQuery->fetch_assoc();
    
    echo json_encode(["status" => "success", "action" => $action, "likes_count" => $row['c']]);
} else {
    echo json_encode(["status" => "error", "message" => "Missing data"]);
}
?>