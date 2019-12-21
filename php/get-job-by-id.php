<?php
include 'db.php';
mysqli_set_charset($c, "utf8");
$id = intval($_POST["id"]);
$items = [];
$sql = "SELECT * FROM jobs WHERE id=" . $id;
$results = $c->query($sql);
if ($results->num_rows > 0) {
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
		array_push($items, $row);
	}
	echo json_encode($items);
} else {
	echo -1;
}
