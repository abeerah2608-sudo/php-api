<?php
require 'db_connect.php';
header('Content-Type: application/json');

$query = $_GET['query'] ?? '';
$current_user_id = $_GET['user_id'] ?? 0;

if ($query) {
    $searchTerm = "%" . $query . "%";
    // Find users matching the name, exclude yourself
    $stmt = $conn->prepare("SELECT user_id, username, profile_pic_url, is_online FROM users WHERE username LIKE ? AND user_id != ?");
    $stmt->bind_param("si", $searchTerm, $current_user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $users = array();
    while($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    echo json_encode(["status" => "success", "users" => $users]);
} else {
    echo json_encode(["status" => "success", "users" => []]);
}
?>