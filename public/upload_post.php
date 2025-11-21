<?php
require 'db_connect.php';
header('Content-Type: application/json');

$user_id = $_POST['user_id'] ?? '';
$caption = $_POST['caption'] ?? '';

if (isset($_FILES['post_image']) && !empty($user_id)) {
    $target_dir = "uploads/";
    $file_extension = pathinfo($_FILES["post_image"]["name"], PATHINFO_EXTENSION);
    // Create a unique name: post_USERID_TIMESTAMP.jpg
    $new_filename = "post_" . $user_id . "_" . time() . "." . $file_extension;
    $target_file = $target_dir . $new_filename;

    if (move_uploaded_file($_FILES["post_image"]["tmp_name"], $target_file)) {
        // Update this IP to your machine's IP
        $full_url = "http://192.168.1.10/socially_api/" . $target_file;

        $stmt = $conn->prepare("INSERT INTO posts (user_id, image_url, caption) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user_id, $full_url, $caption);
        
        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Post uploaded"]);
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