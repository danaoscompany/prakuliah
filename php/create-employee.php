<?php
include 'db.php';
$userID = intval($_POST["user_id"]);
$jobID = intval($_POST["job_id"]);
$salary = intval($_POST["salary"]);
$startWorkDate = $_POST["start_work_date"];
$endWorkDate = $_POST["end_work_date"];
$accessToken = $_POST["access_token"];
$c->query("INSERT INTO employees (user_id, job_id, salary, start_work_date, end_work_date) VALUES (" . $userID . ", " . $jobID . ", " . $salary . ", '" . $startWorkDate . "', '" . $endWorkDate . "')");
$employeeID = mysqli_insert_id($c);
//$c->query("DELETE FROM applications WHERE user_id=" . $userID . " AND job_id=" . $jobID);
$c->query("UPDATE applications SET status=3 WHERE user_id=" . $userID . " AND job_id=" . $jobID);
$employee = $c->query("SELECT * FROM employees WHERE id=" . $employeeID)->fetch_assoc();
$users = $c->query("SELECT * FROM users WHERE id=" . $employee["user_id"]);
if ($users && $users->num_rows > 0) {
	$user = $users->fetch_assoc();
	$employee["name"] = $user["first_name"] . " " . $user["last_name"];
	$employee["profile_picture"] = $user["profile_picture"];
}
$jobs = $c->query("SELECT * FROM jobs WHERE id=" . $jobID);
if ($jobs && $jobs->num_rows > 0) {
	$job = $jobs->fetch_assoc();
	$employee["position"] = $job["title"];
}
$job = $c->query("SELECT * FROM jobs WHERE id=" . $jobID)->fetch_assoc();
$jobName = $job["title"];
$employerID = $job["employer_id"];
$employerName = $c->query("SELECT * FROM employers WHERE id=" . $employerID)->fetch_assoc()["full_name"];
$fcmID = $c->query("SELECT * FROM users WHERE id=" . $userID)->fetch_assoc()["fcm_id"];
$title = "Yay!";
$message = "Selamat! Kamu diterima sebagai <b>" . $jobName . "</b> di Mitra <b>" . $employerName . "</b>.";
$url = 'https://fcm.googleapis.com/v1/projects/prakuliah-faaa8/messages:send';
$fields = array(
    'message' => array(
        'token' => $fcmID,
        'android' => array(
	        'notification' => array(
            	'title' => $title,
            	'body' => $message,
            	'click_action' => "com.prod.prakuliah.APPLICATION_ACCEPTED"
        	),
        	'data' => array(
        		'job_id' => "" . $jobID
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
