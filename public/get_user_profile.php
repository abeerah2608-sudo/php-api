<?php
require 'db_connect.php';
header('Content-Type: application/json');

$user_id = $_GET['user_id'] ?? 0;
$my_id = $_GET['my_id'] ?? 0; // optional: logged-in user for follow status

if ($user_id) {
    $userQuery = $conn->prepare("SELECT user_id, username, profile_pic_url FROM users WHERE user_id = ?");
    $userQuery->bind_param("i", $user_id);
    $userQuery->execute();
    $userResult = $userQuery->get_result()->fetch_assoc();

    if ($userResult) {

        // Posts count
        $stmt = $conn->prepare("SELECT COUNT(*) AS posts_count FROM posts WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $posts_count = $stmt->get_result()->fetch_assoc()['posts_count'];

        // Followers count
        $stmt = $conn->prepare("SELECT COUNT(*) AS followers_count FROM follow_requests WHERE target_id = ? AND status = 'accepted'");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $followers_count = $stmt->get_result()->fetch_assoc()['followers_count'];

        // Following count
        $stmt = $conn->prepare("SELECT COUNT(*) AS following_count FROM follow_requests WHERE requester_id = ? AND status = 'accepted'");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $following_count = $stmt->get_result()->fetch_assoc()['following_count'];

        // 3️⃣ Optional: Check if logged-in user is following this target
        $is_following = false;
        if ($my_id) {
            $stmt = $conn->prepare("SELECT 1 FROM follow_requests WHERE requester_id = ? AND target_id = ? AND status = 'accepted'");
            $stmt->bind_param("ii", $my_id, $user_id);
            $stmt->execute();
            $is_following = $stmt->get_result()->num_rows > 0;
        }

        // Merge counts into result
        $userResult['posts_count'] = (int)$posts_count;
        $userResult['followers_count'] = (int)$followers_count;
        $userResult['following_count'] = (int)$following_count;
        $userResult['is_following'] = $is_following;

        echo json_encode(["status" => "success", "user" => $userResult]);
    } else {
        echo json_encode(["status" => "error", "message" => "User not found"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Missing ID"]);
}
?>

