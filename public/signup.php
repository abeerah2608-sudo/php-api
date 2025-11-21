<?php
require 'db_connect.php';
header('Content-Type: application/json');

// Get Text Data
$username = $_POST['username'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($username) || empty($email) || empty($password)) {
    echo json_encode(["status" => "error", "message" => "Fields required"]);
    exit();
}

// Check Duplicate Email
$checkStmt = $conn->prepare("SELECT email FROM users WHERE email = ?");
$checkStmt->bind_param("s", $email);
$checkStmt->execute();
if ($checkStmt->get_result()->num_rows > 0) {
    echo json_encode(["status" => "error", "message" => "Email exists"]);
    exit();
}

// Handle Image Upload
$profile_pic_url = null;
if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
    $target_dir = "uploads/";
    if (!is_dir($target_dir)) mkdir($target_dir, 0777, true); // Create folder if not exists
    
    $file_extension = pathinfo($_FILES["profile_pic"]["name"], PATHINFO_EXTENSION);
    $new_filename = uniqid() . "." . $file_extension;
    $target_file = $target_dir . $new_filename;
    
    if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file)) {
        // Save the full URL or relative path
        // For Android to access it: http://10.0.2.2/socially_api/uploads/filename.jpg
        $profile_pic_url = "http://192.168.1.10/socially_api/" . $target_file;
    }
}

// Insert User
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
$stmt = $conn->prepare("INSERT INTO users (username, email, password_hash, profile_pic_url) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $username, $email, $hashed_password, $profile_pic_url);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Registered", "user_id" => $stmt->insert_id]);
} else {
    echo json_encode(["status" => "error", "message" => "DB Insert Failed"]);
}

$conn->close();
?>