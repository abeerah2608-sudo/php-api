<?php
require '../db_connect.php';
header('Content-Type: application/json');

// Basic auth or API key recommended: (optional) check here

$sql = "SELECT m.id, m.sender_id, m.receiver_id, m.message_text AS message, u.fcm_token
        FROM messages m
        JOIN users u ON u.user_id = m.receiver_id
        WHERE m.notification_sent = 0
        ORDER BY m.created_at ASC
        LIMIT 100"; // safety cap

$result = $conn->query($sql);
$rows = [];
if ($result) {
    while($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
}
echo json_encode($rows);
$conn->close();
