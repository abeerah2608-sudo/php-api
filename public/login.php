<?php
// login.php
require 'db_connect.php';
header('Content-Type: application/json');

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    echo json_encode(["status" => "error", "message" => "Fields required"]);
    exit();
}

$stmt = $conn->prepare("SELECT user_id, username, password_hash FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    // Verify Password
    if (password_verify($password, $row['password_hash'])) {
        
        // Update Online Status to 1 (True)
        $updateStmt = $conn->prepare("UPDATE users SET is_online = 1 WHERE user_id = ?");
        $updateStmt->bind_param("i", $row['user_id']);
        $updateStmt->execute();

        echo json_encode([
            "status" => "success", 
            "message" => "Login successful",
            "user" => [
                "id" => $row['user_id'],
                "username" => $row['username']
            ]
        ]);
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid credentials"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "User not found"]);
}

$conn->close();
?>
