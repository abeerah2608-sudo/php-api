<?php
require 'db_connect.php';
header('Content-Type: application/json');

$user1 = $_GET['user1_id'] ?? 0; // Me
$user2 = $_GET['user2_id'] ?? 0; // The person I'm talking to

if ($user1 && $user2) {
    // Logic: Get all messages where (Sender is Me AND Receiver is You) OR (Sender is You AND Receiver is Me)
    // Order by Oldest first (like WhatsApp)
    $sql = "SELECT * FROM messages 
            WHERE (sender_id = ? AND receiver_id = ?) 
            OR (sender_id = ? AND receiver_id = ?) 
            ORDER BY created_at ASC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiii", $user1, $user2, $user2, $user1);
    $stmt->execute();
    $result = $stmt->get_result();

    $messages = array();
    while($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }

    echo json_encode(["status" => "success", "messages" => $messages]);
} else {
    echo json_encode(["status" => "error", "message" => "Missing User IDs"]);
}
?>