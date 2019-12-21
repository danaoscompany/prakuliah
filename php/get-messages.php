<?php
include 'db.php';
$myUserID = intval($_POST["my_user_id"]);
$opponentUserID = intval($_POST["opponent_user_id"]);
$start = intval($_POST["start"]);
$results = $c->query("SELECT * FROM messages WHERE (sender_id=" . $myUserID . " AND receiver_id=" . $opponentUserID . ") OR (sender_id=" . $opponentUserID . " AND receiver_id=" . $myUserID . ") ORDER BY id DESC LIMIT " . $start . ",15");
$messages = [];
if ($results && $results->num_rows > 0) {
	while ($row = $results->fetch_assoc()) {
		array_push($messages, $row);
	}
}
echo json_encode($messages);
