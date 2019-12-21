<?php
include 'db.php';
$employerID = intval($_POST["employer_id"]);
$jobs = [];
$results = $c->query("SELECT * FROM jobs WHERE employer_id=" . $employerID);
if ($results && $results->num_rows > 0) {
	while ($row = $results->fetch_assoc()) {
		$job = array(
			'id' => $row["id"],
			'title' => $row["title"]
		);
		array_push($jobs, $job);
	}
}
echo json_encode($jobs);
