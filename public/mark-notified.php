<?php
require 'db_connect.php';
header('Content-Type: application/json');

$id = $_GET['id'] ?? null;
if (!$id) {
    echo json_encode(["status" => "error", "message" => "Missing id"]);
    exit;
}

$stmt = $conn->prepare("UPDATE messages SET notification_sent = 1 WHERE id = ?");
$stmt->bind_param("i", $id);
if ($stmt->execute()) {
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error"]);
}
$conn->close();
