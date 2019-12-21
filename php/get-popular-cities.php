<?php
include 'db.php';
mysqli_set_charset($c, "utf8");
$items = [];
$results = $c->query("SELECT * FROM popular_cities");
if ($results && $results->num_rows > 0) {
	while ($row = $results->fetch_assoc()) {
		$latitude = doubleval($row["latitude"]);
		$longitude = doubleval($row["longitude"]);
		$jobs = $c->query("SELECT * FROM jobs WHERE city_id=" . $row["id"]);
		if ($jobs) {
			$row["jobs_count"] = $jobs->num_rows;
			$totalApplicants = 0;
			while ($job = $jobs->fetch_assoc()) {
				$jobID = intval($job["id"]);
				$applicants = $c->query("SELECT * FROM applications WHERE job_id=" . $jobID);
				if ($applicants) {
					$totalApplicants += $applicants->num_rows;
				}
			}
			$row["applicants_count"] = $totalApplicants;
		} else {
			$row["jobs_count"] = 0;
			$row["applicants_count"] = 0;
		}
		array_push($items, $row);
	}
	echo json_encode($items);
} else {
	echo -1;
}
