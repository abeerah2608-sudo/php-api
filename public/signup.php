<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'db_connect.php';
header('Content-Type: application/json');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["status" => "error", "message" => "Method not allowed"]);
    exit();
}

// Get text fields
$username = $_POST['username'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($username) || empty($email) || empty($password)) {
    echo json_encode(["status" => "error", "message" => "Fields required"]);
    exit();
}

// Check if email exists
$checkStmt = $conn->prepare("SELECT email FROM users WHERE email = ?");
$checkStmt->bind_param("s", $email);
$checkStmt->execute();
if ($checkStmt->get_result()->num_rows > 0) {
    echo json_encode(["status" => "error", "message" => "Email exists"]);
    exit();
}

// Handle image upload
$profile_pic_url = null;
$target_dir = "uploads/"; // Use uploads/ relative to public root
if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);

if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === 0) {
    $file_extension = pathinfo($_FILES["profile_pic"]["name"], PATHINFO_EXTENSION);
    $new_filename = uniqid() . "." . $file_extension;
    $target_file = $target_dir . $new_filename;

    if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file)) {
        $profile_pic_url = $target_file; // store relative path
    }
}

// Insert user into DB
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
$stmt = $conn->prepare(
    "INSERT INTO users (username, email, password_hash, profile_pic_url) VALUES (?, ?, ?, ?)"
);
$stmt->bind_param("ssss", $username, $email, $hashed_password, $profile_pic_url);

if ($stmt->execute()) {
    echo json_encode([
        "status" => "success",
        "message" => "Registered",
        "user_id" => $stmt->insert_id,
        "username" => $username,
        "profile_pic" => $profile_pic_url
    ]);
} else {
    echo json_encode(["status" => "error", "message" => "DB Insert Failed"]);
}

$conn->close();
?>
