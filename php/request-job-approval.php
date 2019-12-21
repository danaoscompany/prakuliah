<?php
include 'db.php';
$jobID = intval($_POST["job_id"]);
$accessToken = $_POST["access_token"];
$c->query("UPDATE jobs SET available=3 WHERE id=" . $jobID);
$results = $c->query("SELECT * FROM admins");
$title = "Lowongan baru";
$message = "Ada lowongan baru terdaftar. Mohon periksa untuk disetujui.";
if ($results && $results->num_rows > 0) {
	while ($row = $results->fetch_assoc()) {
		$fcmID = $row["fcm_id"];
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
	}
}
