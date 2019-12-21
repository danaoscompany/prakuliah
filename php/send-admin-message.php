<?php
include 'db.php';
$senderID = intval($_POST["sender_id"]);
$senderRole = $_POST["sender_role"];
$receiverID = intval($_POST["receiver_id"]);
$receiverRole = $_POST["receiver_role"];
$title = $_POST["title"];
$message = $_POST["message"];
$date = date('Y:m:d H:i:s');
$accessToken = $_POST["access_token"];
$c->query("INSERT INTO messages (sender_id, sender_role, receiver_id, receiver_role, status, title, message, date) VALUES (" . $senderID . ", '" . $senderRole . "', " . $receiverID . ", '" . $receiverRole . "', 0, '" . $title . "', '" . $message . "', '" . $date . "')");
$messageID = mysqli_insert_id($c);
$fcmID = "";
if ($receiverRole == "user") {
    $results = $c->query("SELECT * FROM users WHERE id=" . $receiverID);
    if ($results && $results->num_rows > 0) {
        $row = $results->fetch_assoc();
        $fcmID = $row["fcm_id"];
    }
} else if ($receiverRole == "employer") {
    $results = $c->query("SELECT * FROM employers WHERE id=" . $receiverID);
    if ($results && $results->num_rows > 0) {
        $row = $results->fetch_assoc();
        $fcmID = $row["fcm_id"];
    }
} else if ($receiverRole == "admin") {
    $results = $c->query("SELECT * FROM admins WHERE id=" . $receiverID);
    if ($results && $results->num_rows > 0) {
        $row = $results->fetch_assoc();
        $fcmID = $row["fcm_id"];
    }
}
$url = 'https://fcm.googleapis.com/v1/projects/prakuliah-faaa8/messages:send';
$fields = array(
    'message' => array(
        'token' => $fcmID,
        'data' => array(
            "message" => $message
        ),
        'notification' => array(
            'title' => $title,
            'body' => $message
        )
    )
);
$fields = json_encode($fields);
$headers = array(
    'Authorization: Bearer ' . $accessToken,
    'Content-Type: application/json'
);
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
$result = curl_exec($ch);
curl_close($ch);
echo json_encode($c->query("SELECT * FROM messages WHERE id=" . $messageID)->fetch_assoc());
