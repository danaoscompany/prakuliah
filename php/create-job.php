<?php
include 'db.php';
$categoryID = intval($_POST["category_id"]);
$title = $_POST["title"];
$employerID = intval($_POST["employer_id"]);
$benefits = $_POST["benefits"];
$description = $_POST["description"];
$workingHours = $_POST["working_hours"];
$otherDescription = $_POST["other_description"];
$locationName = $_POST["location_name"];
$cityID = intval($_POST["city_id"]);
$gender = $_POST["gender"];
$salary = intval($_POST["salary"]);
$salaryMonth = intval($_POST["salary_month"]);
$salaryNegotiable = intval($_POST["salary_negotiable"]);
$startWorkDate = $_POST["start_work_date"];
$endWorkDate = $_POST["end_work_date"];
$capacity = intval($_POST["capacity"]);
$minimumAge = intval($_POST["minimum_age"]);
$features = $_POST["features"];
$accessToken = $_POST["access_token"];
// Check if employer has been verified
$results = $c->query("SELECT * FROM employers WHERE id=" . $employerID);
if ($results && $results->num_rows > 0) {
	$row = $results->fetch_assoc();
	$verified = intval($row["verified"]);
	if ($verified == 0) {
		echo -1;
		return;
	}
}

$sql = "INSERT INTO jobs (category_id, title, employer_id, benefits, description, working_hours, other_description, location_name, city_id, gender, salary, salary_month, salary_negotiable, available, start_work_date, end_work_date, capacity, minimum_age, features, date_posted) VALUES (" . $categoryID . ", '" . $title . "', " . $employerID . ", '" . $benefits . "', '" . $description . "', '" . $workingHours . "', '" . $otherDescription . "', '" . $locationName . "', " . $cityID . ", '" . $gender . "', " . $salary . ", " . $salaryMonth . ", " . $salaryNegotiable . ", 3, '" . $startWorkDate . "', '" . $endWorkDate . "', " . $capacity . ", " . $minimumAge . ", '" . $features . "', '" . date('Y:m:d H:i:s') . "')";
$c->query($sql);
$jobID = mysqli_insert_id($c);
if (!file_exists('../userdata/job_images/' . $jobID)) {
    mkdir('../userdata/job_images/' . $jobID, 0777, true);
}
foreach($_FILES["files"]["tmp_name"] as $key=>$tmp_name) {
	move_uploaded_file($_FILES["files"]["tmp_name"][$key], "../userdata/job_images/" . $jobID . "/" . $_FILES["files"]["name"][$key]);
	$c->query("INSERT INTO job_images (job_id, img) VALUES (" . $jobID . ", 'job_images/" . $jobID . "/" . $_FILES["files"]["name"][$key] . "')");
}
$results = $c->query("SELECT * FROM admins");
$title = "Lowongan baru";
$message = "Ada lowongan baru terdaftar. Mohon periksa untuk disetujui.";
if ($results && $results->num_rows > 0) {
	while ($row = $results->fetch_assoc()) {
		$fcmID = $row["fcm_id"];
		$url = 'https://fcm.googleapis.com/v1/projects/prakuliah-faaa8/messages:send';
		$fields = array(
		    'message' => array(
		        'token' => $fcmID,
		        'data' => array(
		        	"job_id" => $jobID
		        ),
        		'notification' => array(
		            'title' => $title,
		            'body' => $message
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
	}
}
$results = $c->query("SELECT * FROM jobs WHERE id=" . $jobID);
if ($results->num_rows > 0) {
	$row = $results->fetch_assoc();
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
	echo json_encode($row);
}
