<?php
include 'db.php';
$role = $_POST["role"];
$userID = intval($_POST["user_id"]);
$latitude = doubleval($_POST["latitude"]);
$longitude = doubleval($_POST["longitude"]);
if ($role == "user") {
	$c->query("UPDATE users SET latitude=" . $latitude . ", longitude=" . $longitude . " WHERE id=" . $userID);
} else if ($role == "admin") {
	$c->query("UPDATE admins SET latitude=" . $latitude . ", longitude=" . $longitude . " WHERE id=" . $userID);
} else if ($role == "employer") {
	$c->query("UPDATE employers SET latitude=" . $latitude . ", longitude=" . $longitude . " WHERE id=" . $userID);
}
