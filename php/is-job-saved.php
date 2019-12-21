<?php
include 'db.php';
$userID = intval($_POST["user_id"]);
$jobID = intval($_POST["job_id"]);
$results = $c->query("SELECT * FROM saved_jobs WHERE user_id=" . $userID . " AND job_id=" . $jobID);
if ($results && $results->num_rows > 0) {
	echo 1;
} else {
	echo 0;
}
