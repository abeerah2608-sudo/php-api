<?php
require 'db_connect.php';
header('Content-Type: application/json');

// Get POST data
$user_id = $_POST['user_id'] ?? '';
$caption = $_POST['caption'] ?? '';

if (isset($_FILES['story_image']) && !empty($user_id)) {

    // 1. Fetch user's profile picture from users table
    $profile_pic_url = null;
    $stmtUser = $conn->prepare("SELECT profile_pic_url FROM users WHERE user_id = ? LIMIT 1");
    $stmtUser->bind_param("i", $user_id);
    $stmtUser->execute();
    $resultUser = $stmtUser->get_result();
    if ($row = $resultUser->fetch_assoc()) {
        $profile_pic_url = $row['profile_pic_url']; // could be null if user has no profile pic
    }
    $stmtUser->close();

    // 2. Upload story image
    $target_dir = "uploads/";
    $file_extension = pathinfo($_FILES["story_image"]["name"], PATHINFO_EXTENSION);
    $new_filename = "story_" . uniqid() . "." . $file_extension;
    $target_file = $target_dir . $new_filename;

    if (move_uploaded_file($_FILES["story_image"]["tmp_name"], $target_file)) {
        // Use Railway public URL instead of local path
        $full_url = "https://php-api-production-28f5.up.railway.app/uploads/" . $new_filename;

        // 3. Insert story into stories table including profile_pic_url
        $stmt = $conn->prepare("INSERT INTO stories (user_id, media_url, caption, profile_pic_url, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("isss", $user_id, $full_url, $caption, $profile_pic_url);

        if ($stmt->execute()) {
            echo json_encode([
                "status" => "success",
                "message" => "Story uploaded",
                "story_url" => $full_url,
                "profile_pic_url" => $profile_pic_url
            ]);
        } else {
            echo json_encode(["status" => "error", "message" => "Database error"]);
        }

        $stmt->close();
    } else {
        echo json_encode(["status" => "error", "message" => "Upload failed"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Missing data"]);
}

$conn->close();
?>
