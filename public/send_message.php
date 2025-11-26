<?php
require 'db_connect.php';
error_reporting(E_ALL);
ini_set("display_errors", 1);

header('Content-Type: application/json');

use Firebase\JWT\JWT;

// Path to your service account JSON
$serviceAccountPath = 'serviceAccount.json';
$serviceAccount = json_decode(file_get_contents($serviceAccountPath), true);

// Get POST data
$sender_id = $_POST['sender_id'] ?? '';
$receiver_id = $_POST['receiver_id'] ?? '';
$message = $_POST['message'] ?? '';
$type = $_POST['type'] ?? 'text';

if (!empty($sender_id) && !empty($receiver_id) && !empty($message)) {

    // Insert message into DB
  // inside your existing send_message.php after validation:
$stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message_text, message_type, notification_sent) VALUES (?, ?, ?, ?, 0)");
$stmt->bind_param("iiss", $sender_id, $receiver_id, $message, $type);

    if ($stmt->execute()) {
        $message_id = $stmt->insert_id;

        // Get receiver FCM token
        $tokenQuery = $conn->prepare("SELECT fcm_token FROM users WHERE user_id = ?");
        $tokenQuery->bind_param("i", $receiver_id);
        $tokenQuery->execute();
        $tokenResult = $tokenQuery->get_result();
        $tokenRow = $tokenResult->fetch_assoc();
        $fcm_token = $tokenRow['fcm_token'] ?? '';

        if (!empty($fcm_token)) {
            // Generate OAuth2 access token using JWT
            $now = time();
            $jwt_payload = [
                "iss" => $serviceAccount['client_email'],
                "scope" => "https://www.googleapis.com/auth/firebase.messaging",
                "aud" => $serviceAccount['token_uri'],
                "iat" => $now,
                "exp" => $now + 3600
            ];

            $jwt = JWT::encode($jwt_payload, $serviceAccount['private_key'], 'RS256');

            // Request access token
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $serviceAccount['token_uri']);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt
            ]));
            $response = json_decode(curl_exec($ch), true);
            curl_close($ch);

            $accessToken = $response['access_token'] ?? '';

            if (!empty($accessToken)) {
                $fcmUrl = "https://fcm.googleapis.com/v1/projects/{$serviceAccount['project_id']}/messages:send";

                $fcmNotification = [
                    "message" => [
                        "token" => $fcm_token,
                        "notification" => [
                            "title" => "New Message",
                            "body" => $message
                        ],
                        "data" => [
                            "sender_id" => $sender_id,
                            "message_id" => $message_id
                        ]
                    ]
                ];

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $fcmUrl);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    "Authorization: Bearer $accessToken",
                    "Content-Type: application/json"
                ]);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
                curl_exec($ch);
                curl_close($ch);
            }
        }

        echo json_encode(["status" => "success", "message_id" => $message_id]);
    } else {
        echo json_encode(["status" => "error", "message" => "DB Insert Failed"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Missing fields"]);
}

$conn->close();



