<?php
require 'db_connect.php';
header('Content-Type: application/json');

$user_id = $_POST['user_id'] ?? '';
$caption = $_POST['caption'] ?? '';

if (isset($_FILES['story_image']) && !empty($user_id)) {
    $target_dir = "uploads/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $file_extension = pathinfo($_FILES["story_image"]["name"], PATHINFO_EXTENSION);
    $new_filename = "story_" . uniqid() . "." . $file_extension;
    $target_file = $target_dir . $new_filename;

    if (move_uploaded_file($_FILES["story_image"]["tmp_name"], $target_file)) {
        // Use Railway public URL
        $full_url = "https://php-api-production-28f5.up.railway.app/uploads/" . $new_filename;

        $stmt = $conn->prepare("INSERT INTO stories (user_id, media_url, caption, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("iss", $user_id, $full_url, $caption);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Story uploaded"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Database error"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Upload failed"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Missing data"]);
}

$conn->close();
?>
