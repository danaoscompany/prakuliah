<?php
include 'db.php';
$jobID = intval($_POST["job_id"]);
$results = $c->query("SELECT * FROM employees WHERE job_id=" . $jobID);
$workers = [];
if ($results && $results->num_rows > 0) {
	while ($row = $results->fetch_assoc()) {
		$users = $c->query("SELECT * FROM users WHERE id=" . $row["user_id"]);
		if ($users && $users->num_rows > 0) {
			$user = $users->fetch_assoc();
			$row["name"] = $user["first_name"] . " " . $user["last_name"];
			$row["profile_picture"] = $user["profile_picture"];
		}
		$jobs = $c->query("SELECT * FROM jobs WHERE id=" . $jobID);
		if ($jobs && $jobs->num_rows > 0) {
			$job = $jobs->fetch_assoc();
			$row["position"] = $job["title"];
		}
		array_push($workers, $row);
	}
}
echo json_encode($workers);
