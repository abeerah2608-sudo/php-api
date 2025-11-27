<?php
require 'db_connect.php';
header('Content-Type: application/json');

$user_id = $_GET['user_id'] ?? 0;
$my_id = $_GET['my_id'] ?? 0; 

if ($user_id) {
    // 1. Get User Details
    $userQuery = $conn->prepare("SELECT user_id, username, profile_pic_url FROM users WHERE user_id = ?");
    $userQuery->bind_param("i", $user_id);
    $userQuery->execute();
    $userResult = $userQuery->get_result()->fetch_assoc();

    if ($userResult) {
        $baseUrl = "https://php-api-production-28f5.up.railway.app/uploads/";
        if (!empty($userResult['profile_pic_url'])) {
            $userResult['profile_pic_url'] = $baseUrl . $userResult['profile_pic_url'];
        }

        // Counts
        $stmt = $conn->prepare("SELECT COUNT(*) AS posts_count FROM posts WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $userResult['posts_count'] = $stmt->get_result()->fetch_assoc()['posts_count'];

        $stmt = $conn->prepare("SELECT COUNT(*) AS followers_count FROM follow_requests WHERE target_id = ? AND status='accepted'");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $userResult['followers_count'] = $stmt->get_result()->fetch_assoc()['followers_count'];

        $stmt = $conn->prepare("SELECT COUNT(*) AS following_count FROM follow_requests WHERE requester_id = ? AND status='accepted'");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $userResult['following_count'] = $stmt->get_result()->fetch_assoc()['following_count'];

        // Check if logged-in user follows target
        $is_following = false;
        if ($my_id) {
            $stmt = $conn->prepare("SELECT 1 FROM follow_requests WHERE requester_id=? AND target_id=? AND status='accepted'");
            $stmt->bind_param("ii", $my_id, $user_id);
            $stmt->execute();
            $is_following = $stmt->get_result()->num_rows > 0;
        }

        $userResult['is_following'] = $is_following;

        echo json_encode(["status"=>"success","user"=>$userResult]);
        // 2. Get Stats (Bonus/Optional for now, returning 0s)
        // You can implement real COUNT(*) queries here later for followers/following
        $userResult['posts_count'] = 0;
        $userResult['followers_count'] = 0;
        $userResult['following_count'] = 0;
        
        echo json_encode(["status" => "success", "user" => $userResult]);
    } else {
        echo json_encode(["status"=>"error","message"=>"User not found"]);
        echo json_encode(["status" => "error", "message" => "User not found"]);
    }
} else {
    echo json_encode(["status"=>"error","message"=>"Missing ID"]);
    echo json_encode(["status" => "error", "message" => "Missing ID"]);
}
?>
