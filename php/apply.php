<?php
include 'db.php';
$userID = intval($_POST["user_id"]);
$jobID = intval($_POST["job_id"]);
$startWorkDate = $_POST["start_work_date"];
$startWorkDateType = intval($_POST["start_work_date_type"]);
$endWorkDate = $_POST["end_work_date"];
$endWorkDateType = intval($_POST["end_work_date_type"]);
$salaryPerMonth = intval($_POST["salary_per_month"]);
$salaryMonth = intval($_POST["salary_month"]);
$description = $_POST["description"];
$accessToken = $_POST["access_token"];
$employerID = intval($c->query("SELECT * FROM jobs WHERE id=" . $jobID)->fetch_assoc()["employer_id"]);
$date = date('Y:m:d H:i:s');
$sql = "INSERT INTO applications (job_id, employer_id, user_id, start_work_date, start_work_date_type, end_work_date, end_work_date_type, salary_per_month, salary_month, description, date, status) VALUES (" . $jobID . ", " . $employerID . ", " . $userID . ", '" . $startWorkDate . "', " . $startWorkDateType . ", '" . $endWorkDate . "', " . $endWorkDateType . ", " . $salaryPerMonth . ", " . $salaryMonth . ", '" . $description . "', '" . $date . "', 1)";
$c->query($sql);
$applicationID = mysqli_insert_id($c);
$cvs = $c->query("SELECT * FROM cv WHERE user_id=" . $userID);
$cv = NULL;
$cvID = 0;
if ($cvs && $cvs->num_rows > 0) {
	$cv = $cvs->fetch_assoc();
	$cvID = $cv["id"];
	$users = $c->query("SELECT * FROM users WHERE id=" . $cv["user_id"]);
		if ($users && $users->num_rows > 0) {
			$user = $users->fetch_assoc();
			$cv["verified"] = $user["verified"];
			$cv["user_info"] = json_encode($user);
		}
		$univs = $c->query("SELECT * FROM universities WHERE id=" . $cv["target_university_id"]);
		if ($univs && $univs->num_rows > 0) {
			$univ = $univs->fetch_assoc();
			$cv["target_university_name"] = $univ["name"];
		}
		$certifications = $c->query("SELECT * FROM cv_certifications WHERE cv_id=" . $cv["id"]);
		if ($certifications && $certifications->num_rows > 0) {
			$certificationsJSON = [];
			while ($certification = $certifications->fetch_assoc()) {
				array_push($certificationsJSON, $certification);
			}
			$cv["certifications"] = json_encode($certificationsJSON);
		}
		$organizations = $c->query("SELECT * FROM cv_organizations WHERE cv_id=" . $cv["id"]);
		if ($organizations && $organizations->num_rows > 0) {
			$organizationsJSON = [];
			while ($organization = $organizations->fetch_assoc()) {
				array_push($organizationsJSON, $organization);
			}
			$cv["organizations"] = json_encode($organizationsJSON);
		}
		$skills = $c->query("SELECT * FROM cv_skills WHERE cv_id=" . $cv["id"]);
		if ($skills && $skills->num_rows > 0) {
			$skillsJSON = [];
			while ($skill = $skills->fetch_assoc()) {
				array_push($skillsJSON, $skill);
			}
			$cv["skills"] = json_encode($skillsJSON);
		}
		$languages = $c->query("SELECT * FROM cv_languages WHERE cv_id=" . $cv["id"]);
		if ($languages && $languages->num_rows > 0) {
			$languagesJSON = [];
			while ($language = $languages->fetch_assoc()) {
				array_push($languagesJSON, $language);
			}
			$cv["languages"] = json_encode($languagesJSON);
		}
		$experiences = $c->query("SELECT * FROM cv_experiences WHERE cv_id=" . $cv["id"]);
		if ($experiences && $experiences->num_rows > 0) {
			$experiencesJSON = [];
			while ($experience = $experiences->fetch_assoc()) {
				array_push($experiencesJSON, $experience);
			}
			$cv["experiences"] = json_encode($experiencesJSON);
		}
}
$user = $c->query("SELECT * FROM users WHERE id=" . $userID)->fetch_assoc();
$employer = $c->query("SELECT * FROM employers WHERE id=" . $employerID)->fetch_assoc();
$fcmID = $employer["fcm_id"];
$jobName = $c->query("SELECT * FROM jobs WHERE id=" . $jobID)->fetch_assoc()["title"];
$title = "Ada lamaran baru";
$message = "Seseorang bernama <b>" . $user["first_name"] . " " . $user["last_name"] . "</b> sedang mengajukan lamaran";
$url = 'https://fcm.googleapis.com/v1/projects/prakuliah-faaa8/messages:send';
$fields = array(
    'message' => array(
        'token' => $fcmID,
        'android' => array(
    	    'notification' => array(
    	        'title' => $title,
	            'body' => $message,
            	"click_action" => "com.prod.prakuliah.NEW_JOB"
        	),
        	'data' => array(
    	        "action" => "com.prod.prakuliah.NEW_JOB",
    	        "cv_id" => "" . $cvID,
    	        "cv" => json_encode($cv),
    	        "job_id" => "" . $jobID,
    	        "job_name" => $jobName,
    	        "salary" => "" . $salaryPerMonth,
    	        "salary_month" => "" . $salaryMonth,
    	        "start_work_date" => $startWorkDate,
	            "end_work_date" => $endWorkDate
	        )
        )
    )
);
$fields = json_encode($fields);
$headers = array(
    'Authorization: Bearer ' . $accessToken,
    'Content-Type: application/json'
);
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
$result = curl_exec($ch);
curl_close($ch);
