<?php
include 'db.php';
$employerID = intval($_POST["employer_id"]);
$jobs = [];
$results = $c->query("SELECT * FROM jobs WHERE employer_id=" . $employerID);
if ($results && $results->num_rows > 0) {
	while ($row = $results->fetch_assoc()) {
		$images = $c->query("SELECT * FROM job_images WHERE job_id=" . $row["id"]);
		if ($images && $images->num_rows > 0) {
			$image = $images->fetch_assoc();
			$row["img_path"] = $image["img"];
		}
		$employers = $c->query("SELECT * FROM employers WHERE id=" . $row["employer_id"]);
		if ($employers && $employers->num_rows > 0) {
			$employer = $employers->fetch_assoc();
			$row["employer"] = $employer["full_name"];
			$row["employer_fcm_id"] = $employer["fcm_id"];
			$row["employer_verified"] = $employer["verified"];
		}
		$applicants = [];
		$applications = $c->query("SELECT * FROM applications WHERE job_id=" . $row["id"]);
		if ($applications && $applications->num_rows > 0) {
			while ($application = $applications->fetch_assoc()) {
				$applicantID = $application["user_id"];
				$users = $c->query("SELECT * FROM users WHERE id=" . $applicantID);
				$profilePicturePath = "";
				if ($users && $users->num_rows > 0) {
					$user = $users->fetch_assoc();
					$profilePicturePath = $user["profile_picture"];
				}
				$applicant = array(
					'id' => $applicantID,
					'profile_picture' => $profilePicturePath
				);
				array_push($applicants, $applicant);
			}
		}
		$row["applicants"] = $applicants;
		$row["total_applicants"] = $applications->num_rows;
		array_push($jobs, $row);
	}
}
echo json_encode($jobs);
