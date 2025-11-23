<?php
require 'db_connect.php';
header('Content-Type: application/json');

$sender_id = $_POST['sender_id'] ?? '';
$receiver_id = $_POST['receiver_id'] ?? '';
$message = $_POST['message'] ?? '';
$type = $_POST['type'] ?? 'text'; // Default to text

if (!empty($sender_id) && !empty($receiver_id) && !empty($message)) {
    
    $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message_text, message_type) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $sender_id, $receiver_id, $message, $type);
    
    if ($stmt->execute()) {
        $message_id = $stmt->insert_id;

        // Send FCM notification to receiver
        $tokenQuery = $conn->prepare("SELECT fcm_token FROM users WHERE user_id = ?");
        $tokenQuery->bind_param("i", $receiver_id);
        $tokenQuery->execute();
        $tokenResult = $tokenQuery->get_result();
        $tokenRow = $tokenResult->fetch_assoc();
        $fcm_token = $tokenRow['fcm_token'] ?? '';

        if (!empty($fcm_token)) {
            $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
            $serverKey = 'YOUR_SERVER_KEY_HERE'; // Replace with your Firebase server key

            $notification = [
                'title' => 'New Message',
                'body' => $message
            ];

            $data = [
                'sender_id' => $sender_id,
                'message_id' => $message_id
            ];

            $fcmNotification = [
                'to' => $fcm_token,
                'notification' => $notification,
                'data' => $data
            ];

            $headers = [
                'Authorization: key=' . $serverKey,
                'Content-Type: application/json'
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $fcmUrl);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
            curl_exec($ch);
            curl_close($ch);
        }

        // Return the inserted message ID
        echo json_encode(["status" => "success", "message_id" => $message_id]);
    } else {
        echo json_encode(["status" => "error", "message" => "DB Insert Failed"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Missing fields"]);
}
?>
