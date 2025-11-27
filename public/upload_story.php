<?php
require 'db_connect.php';
header('Content-Type: application/json');

// Get POST fields
$user_id = $_POST['user_id'] ?? '';
$caption = $_POST['caption'] ?? '';

// Validate input
if (!isset($_FILES['story_image']) || empty($user_id)) {
    echo json_encode(["status" => "error", "message" => "Missing data"]);
    exit();
}

$target_dir = "uploads/";
if (!file_exists($target_dir)) {
    mkdir($target_dir, 0777, true);
}

// Upload story image
$file_extension = pathinfo($_FILES["story_image"]["name"], PATHINFO_EXTENSION);
$new_filename = "story_" . uniqid() . "." . $file_extension;
$target_file = $target_dir . $new_filename;

if (!move_uploaded_file($_FILES["story_image"]["tmp_name"], $target_file)) {
    echo json_encode(["status" => "error", "message" => "Upload failed"]);
    exit();
}

// Full story image URL
$story_url = "https://php-api-production-28f5.up.railway.app/uploads/" . $new_filename;

// -----------------------------------------
// FETCH USER PROFILE PIC
// -----------------------------------------
$stmt_user = $conn->prepare("SELECT profile_pic_url FROM users WHERE user_id = ?");
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$result = $stmt_user->get_result();

$profile_pic_url = "";
if ($row = $result->fetch_assoc()) {
    $profile_pic_url = $row['profile_pic_url'];

    // If stored relative, convert to FULL URL
    if (!empty($profile_pic_url) && !str_starts_with($profile_pic_url, "http")) {
        $profile_pic_url = "https://php-api-production-28f5.up.railway.app/" . ltrim($profile_pic_url, "/");
    }
}

$stmt_user->close();


$stmt = $conn->prepare(
    "INSERT INTO stories (user_id, media_url, caption, created_at, profile_pic_url)
     VALUES (?, ?, ?, NOW(), ?)"
);
$stmt->bind_param("isss", $user_id, $story_url, $caption, $profile_pic_url);

if ($stmt->execute()) {
    echo json_encode([
        "status" => "success",
        "message" => "Story uploaded",
        "story_url" => $story_url,
        "profile_pic_url" => $profile_pic_url
    ]);
} else {
    echo json_encode(["status" => "error", "message" => "Database error"]);
}

$stmt->close();
$conn->close();
?>
