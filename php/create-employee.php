<?php
include 'db.php';
$userID = intval($_POST["user_id"]);
$jobID = intval($_POST["job_id"]);
$salary = intval($_POST["salary"]);
$startWorkDate = $_POST["start_work_date"];
$endWorkDate = $_POST["end_work_date"];
$c->query("INSERT INTO employees (user_id, job_id, salary, start_work_date, end_work_date) VALUES (" . $userID . ", " . $jobID . ", " . $salary . ", '" . $startWorkDate . "', '" . $endWorkDate . "')");
$employeeID = mysqli_insert_id($c);
$c->query("DELETE FROM applications WHERE user_id=" . $userID . " AND job_id=" . $jobID);
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
echo json_encode($employee);
