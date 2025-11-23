$target_dir = "uploads/";
if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);

if (isset($_FILES['post_image']) && !empty($user_id)) {
    $file_extension = pathinfo($_FILES["post_image"]["name"], PATHINFO_EXTENSION);
    $new_filename = "post_" . $user_id . "_" . time() . "." . $file_extension;
    $target_file = $target_dir . $new_filename;

    if (move_uploaded_file($_FILES["post_image"]["tmp_name"], $target_file)) {
        // Only now insert into DB
        $full_url = "https://php-api-production-28f5.up.railway.app/" . $target_file;

        $stmt = $conn->prepare("INSERT INTO posts (user_id, image_url, caption) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user_id, $full_url, $caption);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Post uploaded"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Database error"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Upload failed"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Missing data"]);
}
