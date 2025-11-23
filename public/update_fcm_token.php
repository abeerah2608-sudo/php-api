<?php
require 'db_connect.php';
header('Content-Type: application/json');

// Get POST data
$user_id = $_POST['user_id'] ?? '';
$token = $_POST['fcm_token'] ?? '';

if (!empty($user_id) && !empty($token)) {
    $stmt = $conn->prepare("UPDATE users SET fcm_token = ? WHERE user_id = ?");
    $stmt->bind_param("si", $token, $user_id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Token updated"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Database error"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Missing data"]);
}

$conn->close();
