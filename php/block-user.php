<?php
include 'db.php';
$userID = intval($_POST["user_id"]);
$role = $_POST["role"];
$reason = $_POST["reason"];
$c->query("INSERT INTO block_reason (user_id, role, reason) VALUES (" . $userID . ", '" . $role . "', '" . $reason . "')");
if ($role == "user") {
	$c->query("UPDATE users SET blocked=1 WHERE id=" . $userID);
} else if ($role == "employer") {
	$c->query("UPDATE employers SET blocked=1 WHERE id=" . $userID);
}
