<?php
require 'db_connect.php';
header('Content-Type: application/json');

$sender_id = $_POST['sender_id'] ?? '';
$receiver_id = $_POST['receiver_id'] ?? '';

if (isset($_FILES['image']) && !empty($sender_id) && !empty($receiver_id)) {
    $target_dir = "uploads/";
    $file_extension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
    $new_filename = "chat_" . uniqid() . "." . $file_extension;
    $target_file = $target_dir . $new_filename;

    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        // 1. Get Full URL
        // UPDATE THIS IP to match your computer's IP
        $full_url = "http://192.168.1.10/socially_api/" . $target_file;

        // 2. Insert into DB (Type = 'image')
        $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message_text, media_url, message_type) VALUES (?, ?, '', ?, 'image')");
        $stmt->bind_param("iis", $sender_id, $receiver_id, $full_url);
        
        if ($stmt->execute()) {
            echo json_encode(["status" => "success"]);
        } else {
            echo json_encode(["status" => "error", "message" => "DB Error"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Upload Failed"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Missing data"]);
}
?>