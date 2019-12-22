<?php
include 'db.php';
$results = $c->query("SELECT * FROM users WHERE recommended=1 AND blocked=0");
$users = [];
if ($results && $results->num_rows > 0) {
	while ($row = $results->fetch_assoc()) {
		$cvs = $c->query("SELECT * FROM cv WHERE user_id=" . $row["id"]);
		if ($cvs && $cvs->num_rows > 0) {
			$cv = $cvs->fetch_assoc();
			$row["school_name"] = $cv["school_name"];
			$row["major"] = $cv["major"];
			$row["graduation_year"] = $cv["graduation_year"];
		}
		array_push($users, $row);
	}
}
echo json_encode($users);
