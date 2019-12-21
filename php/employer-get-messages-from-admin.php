<?php
include 'db.php';
$employerID = intval($_POST["employer_id"]);
$start = intval($_POST["start"]);
$length = intval($_POST["length"]);
$results = $c->query("SELECT * FROM messages WHERE receiver_id=" . $employerID . " AND receiver_role='employer' ORDER BY date DESC LIMIT " . $start . "," . $length);
$messages = [];
if ($results && $results->num_rows > 0) {
	while ($row = $results->fetch_assoc()) {
		$admins = $c->query("SELECT * FROM admins WHERE id=" . $row["sender_id"]);
		if ($admins && $admins->num_rows > 0) {
			$admin = $admins->fetch_assoc();
			$row["sender_name"] = $admin["first_name"] . " " . $admin["last_name"];
		}
		$employers = $c->query("SELECT * FROM employers WHERE id=" . $row["receiver_id"]);
		if ($employers && $employers->num_rows > 0) {
			$employer = $employers->fetch_assoc();
			$row["receiver_name"] = $employer["full_name"];
		}
		array_push($messages, $row);
	}
}
echo json_encode($messages);
