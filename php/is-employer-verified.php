<?php
include 'db.php';
$id = intval($_POST["id"]);
$results = $c->query("SELECT * FROM employers WHERE id=" . $id);
if ($results && $results->num_rows > 0) {
	$row = $results->fetch_assoc();
	$verified = intval($row["verified"]);
	echo $verified;
} else {
	echo 0;
}
