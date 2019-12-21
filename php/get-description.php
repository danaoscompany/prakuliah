<?php
include 'db.php';
$role = $_POST["role"];
$id = intval($_POST["id"]);
if ($role == "employer") {
	$results = $c->query("SELECT * FROM employers WHERE id=" . $id);
	if ($results && $results->num_rows > 0) {
		$row = $results->fetch_assoc();
		echo $row["biodata"];
	}
}
