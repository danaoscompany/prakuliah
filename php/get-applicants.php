<?php
include 'db.php';
$jobID = intval($_POST["id"]);
$results = $c->query("SELECT * FROM applications WHERE job_id=" . $jobID);
$applicants = [];
if ($results && $results->num_rows > 0) {
	while ($row = $results->fetch_assoc()) {
		$userID = intval($row["user_id"]);
		$users = $c->query("SELECT * FROM users WHERE id=" . $userID);
		if ($users && $users->num_rows > 0) {
			$user = $users->fetch_assoc();
			$cvs = $c->query("SELECT * FROM cv WHERE user_id=" . $userID);
			if ($cvs && $cvs->num_rows > 0) {
				$cv = $cvs->fetch_assoc();
				$user["last_education"] = $cv["last_education"];
				$user["major"] = $cv["major"];
				$user["graduation_year"] = $cv["graduation_year"];
				$user["description"] = $cv["description"];
			}
			$user["application_date_posted"] = $row["date"];
			$applications = $c->query("SELECT * FROM applications WHERE job_id=" . $jobID . " AND user_id=" . $userID);
			if ($applications && $applications->num_rows > 0) {
				$application = $applications->fetch_assoc();
				$user["start_work_date"] = $application["start_work_date"];
				$user["end_work_date"] = $application["end_work_date"];
				$user["salary_per_month"] = $application["salary_per_month"];
				$user["salary_month"] = $application["salary_month"];
			}
			array_push($applicants, $user);
		}
	}
}
echo json_encode($applicants);
