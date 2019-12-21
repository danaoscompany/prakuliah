<?php
include 'db.php';
$start = intval($_POST["start"]);
$length = intval($_POST["length"]);
$ascending = intval($_POST["ascending"]);
$categoryID = intval($_POST["category_id"]);
$cityID = intval($_POST["city_id"]);
$c->query("DROP PROCEDURE IF EXISTS GetJobs;");
$c->query("DELIMITER @@");
$c->query("
CREATE PROCEDURE GetJobs()
BEGIN
	DECLARE finished INT DEFAULT 0;
	DECLARE tmp_job_id INT;
	DECLARE tmp_img_path TEXT DEFAULT '';
    DECLARE tmp_employer TEXT DEFAULT '';
    DECLARE tmp_employer_fcm_id TEXT DEFAULT '';
    DECLARE tmp_employer_verified INT DEFAULT 0;
    DECLARE tmp_category_id INT DEFAULT 0;
    DECLARE tmp_title TEXT DEFAULT '';
    DECLARE tmp_employer_id INT DEFAULT 0;
    DECLARE tmp_description TEXT DEFAULT '';
    DECLARE tmp_location_name text DEFAULT '';
    DECLARE tmp_city_id INT DEFAULT 0;
    DECLARE tmp_gender VARCHAR(1) DEFAULT '';
    DECLARE tmp_salary INT DEFAULT 0;
    DECLARE tmp_salary_month INT DEFAULT 0;
    DECLARE tmp_available INT DEFAULT 0;
    DECLARE tmp_start_work_date DATETIME;
    DECLARE tmp_end_work_date DATETIME;
    DECLARE tmp_capacity INT DEFAULT 0;
    DECLARE tmp_minimum_age INT DEFAULT 0;
    DECLARE tmp_features TEXT DEFAULT '';
    DECLARE tmp_date_posted DATETIME;
    DECLARE total_jobs INT DEFAULT 0;
    DECLARE employee_count INT DEFAULT 0;
    DECLARE c CURSOR FOR SELECT id, category_id, title, employer_id, description, location_name, city_id, gender, salary, salary_month, available, start_work_date, end_work_date, capacity, minimum_age, features, date_posted FROM jobs;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET finished=1;
	DROP TEMPORARY TABLE IF EXISTS job_employee_count;
	CREATE TEMPORARY TABLE job_employee_count (id INT NOT NULL, img_path TEXT DEFAULT NULL, employer TEXT DEFAULT NULL, employer_fcm_id TEXT DEFAULT NULL, employer_verified INT DEFAULT NULL, category_id int(11) DEFAULT NULL, title text DEFAULT NULL, employer_id int(11) DEFAULT NULL, description text DEFAULT NULL, location_name text DEFAULT NULL, city_id int(11) DEFAULT NULL, gender varchar(1) DEFAULT NULL, salary int(11) DEFAULT NULL, salary_month int(11) DEFAULT NULL, available int(11) DEFAULT NULL, start_work_date datetime DEFAULT NULL, end_work_date datetime DEFAULT NULL, capacity int(11) DEFAULT NULL, minimum_age int(11) DEFAULT NULL, features text DEFAULT NULL, date_posted datetime DEFAULT NULL, employee_count INT NOT NULL);
    SELECT COUNT(*) FROM jobs INTO total_jobs;
    SET @counter = 0;
    OPEN c;
    WHILE @counter < total_jobs DO
    	FETCH c INTO tmp_job_id, tmp_category_id, tmp_title, tmp_employer_id, tmp_description, tmp_location_name, tmp_city_id, tmp_gender, tmp_salary, tmp_salary_month, tmp_available, tmp_start_work_date, tmp_end_work_date, tmp_capacity, tmp_minimum_age, tmp_features, tmp_date_posted;
        SELECT COUNT(*) INTO employee_count FROM applications WHERE job_id=tmp_job_id;
        SELECT img INTO tmp_img_path FROM job_images WHERE job_id=tmp_job_id LIMIT 1;
        SELECT full_name, fcm_id, verified INTO tmp_employer, tmp_employer_fcm_id, tmp_employer_verified FROM employers WHERE id=tmp_employer_id;
        INSERT INTO job_employee_count VALUES (tmp_job_id, tmp_img_path, tmp_employer, tmp_employer_fcm_id, tmp_employer_verified, tmp_category_id, tmp_title, tmp_employer_id, tmp_description, tmp_location_name, tmp_city_id, tmp_gender, tmp_salary, tmp_salary_month, tmp_available, tmp_start_work_date, tmp_end_work_date, tmp_capacity, tmp_minimum_age, tmp_features, tmp_date_posted, employee_count);
        SET @counter = @counter+1;
    END WHILE;
    CLOSE c;
    SELECT * FROM job_employee_count WHERE category_id=" . $categoryID . " AND city_id=" . $cityID . ";
    DROP TEMPORARY TABLE job_employee_count;
END;
");
$c->query("@@");
$c->query("DELIMITER ;");
$results = $c->query("call GetJobs()");
$jobs = array();
if ($results && $results->num_rows > 0) {
	while ($row = $results->fetch_assoc()) {
		$images = $c->query("SELECT * FROM job_images WHERE job_id=" . $row["id"]);
		if ($images && $images->num_rows > 0) {
			$image = $images->fetch_assoc();
			$row["img_path"] = $image["img"];
		}
		$jobsJSON = $c->query("SELECT * FROM jobs WHERE id=" . $row["id"]);
		if ($jobsJSON && $jobsJSON->num_rows > 0) {
			$job = $jobsJSON->fetch_assoc();
			$employers = $c->query("SELECT * FROM employers WHERE id=" . $job["employer_id"]);
			if ($employers && $employers->num_rows > 0) {
				$employer = $employers->fetch_assoc();
				$row["employer"] = $employer["full_name"];
				$row["employer_fcm_id"] = $employer["fcm_id"];
				$row["employer_verified"] = $employer["verified"];
			}
			$row["category_id"] = $job["category_id"];
			$row["title"] = $job["title"];
			$row["employer_id"] = $job["employer_id"];
			$row["description"] = $job["description"];
			$row["location_name"] = $job["location_name"];
			$row["city_id"] = $job["city_id"];
			$row["gender"] = $job["gender"];
			$row["salary"] = $job["salary"];
			$row["salary_month"] = $job["salary_month"];
			$row["available"] = $job["available"];
			$row["start_work_date"] = $job["start_work_date"];
			$row["end_work_date"] = $job["end_work_date"];
			$row["capacity"] = $job["capacity"];
			$row["minimum_age"] = $job["minimum_age"];
			$row["features"] = $job["features"];
			$row["date_posted"] = $job["date_posted"];
		}
		array_push($jobs, $row);
	}
}
if ($ascending == 1) {
	usort($jobs, function($job1, $job2) {
		$job1EmployeeCount = $job1["employee_count"];
		$job2EmployeeCount = $job2["employee_count"];
		if ($job1EmployeeCount > $job2EmployeeCount) {
			return -1;
		} else if ($job1EmployeeCount < $job2EmployeeCount) {
			return 1;
		} else {
			return 0;
		}
	});
} else if ($ascending == 0) {
	usort($jobs, function($job1, $job2) {
		$job1EmployeeCount = $job1["employee_count"];
		$job2EmployeeCount = $job2["employee_count"];
		if ($job1EmployeeCount > $job2EmployeeCount) {
			return 1;
		} else if ($job1EmployeeCount < $job2EmployeeCount) {
			return -1;
		} else {
			return 0;
		}
	});
}
echo json_encode($jobs);
