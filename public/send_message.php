<?php
require 'db_connect.php';
header('Content-Type: application/json');

$sender_id = $_POST['sender_id'] ?? '';
$receiver_id = $_POST['receiver_id'] ?? '';
$message = $_POST['message'] ?? '';
$type = $_POST['type'] ?? 'text'; // Default to text

if (!empty($sender_id) && !empty($receiver_id) && !empty($message)) {
    
    $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message_text, message_type) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $sender_id, $receiver_id, $message, $type);
    
    if ($stmt->execute()) {
        // Return the ID so we can confirm it was sent
        echo json_encode(["status" => "success", "message_id" => $stmt->insert_id]);
    } else {
        echo json_encode(["status" => "error", "message" => "DB Insert Failed"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Missing fields"]);
}
?>