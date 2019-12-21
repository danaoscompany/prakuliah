<?php
include 'db.php';
$start = intval($_POST["start"]);
$length = intval($_POST["length"]);
$userID = intval($_POST["user_id"]);
$role = $_POST["role"];
$targetRole = $_POST["target_role"];
$messages = [];
$c->query("CREATE TEMPORARY TABLE tmp SELECT * FROM messages WHERE (sender_role='" . $role . "' AND sender_id=" . $userID . " AND receiver_role='" . $targetRole . "') OR (sender_role='" . $targetRole . "' AND receiver_role='" . $role . "' AND receiver_id=" . $userID . ") ORDER BY date DESC LIMIT " . $start . "," . $length);
$results = $c->query("SELECT * FROM tmp GROUP BY receiver_id");
$c->query("DROP TEMPORARY TABLE tmp");
if ($results && $results->num_rows > 0) {
	while ($row = $results->fetch_assoc()) {
		$senderId = intval($row["sender_id"]);
		$senderRole = $row["sender_role"];
		$receiverId = intval($row["receiver_id"]);
		$receiverRole = $row["receiver_role"];
		if ($senderRole == 'admin') {
			$admins = $c->query("SELECT * FROM admins WHERE id=" . $senderId);
			if ($admins && $admins->num_rows > 0) {
				$admin = $admins->fetch_assoc();
				$row["sender_name"] = $admin["first_name"] . " " . $admin["last_name"];
			}
		} else if ($senderRole == 'user') {
			$users = $c->query("SELECT * FROM users WHERE id=" . $senderId);
			if ($users && $users->num_rows > 0) {
				$user = $users->fetch_assoc();
				$row["sender_name"] = $user["first_name"] . " " . $user["last_name"];
			}
		} else if ($senderRole == 'employer') {
			$employers = $c->query("SELECT * FROM employers WHERE id=" . $senderId);
			if ($employers && $employers->num_rows > 0) {
				$employer = $employers->fetch_assoc();
				$row["sender_name"] = $employer["full_name"];
			}
		}
		if ($receiverRole == 'admin') {
			$admins = $c->query("SELECT * FROM admins WHERE id=" . $receiverId);
			if ($admins && $admins->num_rows > 0) {
				$admin = $admins->fetch_assoc();
				$row["receiver_name"] = $admin["first_name"] . " " . $admin["last_name"];
			}
		} else if ($receiverRole == 'user') {
			$users = $c->query("SELECT * FROM users WHERE id=" . $receiverId);
			if ($users && $users->num_rows > 0) {
				$user = $users->fetch_assoc();
				$row["receiver_name"] = $user["first_name"] . " " . $user["last_name"];
			}
		} else if ($receiverRole == 'employer') {
			$employers = $c->query("SELECT * FROM employers WHERE id=" . $receiverId);
			if ($employers && $employers->num_rows > 0) {
				$employer = $employers->fetch_assoc();
				$row["receiver_name"] = $employer["full_name"];
			}
		}
		array_push($messages, $row);
	}
}

echo json_encode($messages);
