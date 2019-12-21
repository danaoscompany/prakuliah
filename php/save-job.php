<?php
include 'db.php';
$userID = intval($_POST["user_id"]);
$jobID = intval($_POST["job_id"]);
$results = $c->query("SELECT * FROM saved_jobs WHERE user_id=" . $userID . " AND job_id=" . $jobID);
if ($results && $results->num_rows > 0) {
} else {
	$c->query("INSERT INTO saved_jobs (user_id, job_id, date) VALUES (" . $userID . ", " . $jobID . ", '" . date('Y:m:d H:i:s') . "')");
}
