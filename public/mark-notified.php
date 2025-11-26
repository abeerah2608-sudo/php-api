<?php
require 'db_connect.php';
header('Content-Type: application/json');

$messageId = $_GET['id'] ?? null;
if (!$messageId) {
    echo json_encode(["status" => "error", "message" => "Missing id"]);
    exit;
}

$stmt = $conn->prepare("UPDATE messages SET notification_sent = 1 WHERE message_id = ?");
$stmt->bind_param("i", $messageId);

if ($stmt->execute()) {
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error"]);
}

$conn->close();
