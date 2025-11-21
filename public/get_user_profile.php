<?php
require 'db_connect.php';
header('Content-Type: application/json');

$user_id = $_GET['user_id'] ?? 0;

if ($user_id) {
    // 1. Get User Details
    $userQuery = $conn->prepare("SELECT user_id, username, profile_pic_url FROM users WHERE user_id = ?");
    $userQuery->bind_param("i", $user_id);
    $userQuery->execute();
    $userResult = $userQuery->get_result()->fetch_assoc();

    if ($userResult) {
        // 2. Get Stats (Bonus/Optional for now, returning 0s)
        // You can implement real COUNT(*) queries here later for followers/following
        $userResult['posts_count'] = 0;
        $userResult['followers_count'] = 0;
        $userResult['following_count'] = 0;
        
        echo json_encode(["status" => "success", "user" => $userResult]);
    } else {
        echo json_encode(["status" => "error", "message" => "User not found"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Missing ID"]);
}
?>