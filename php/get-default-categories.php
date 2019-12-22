<?php
include 'db.php';
$categories = [];
$results = $c->query("SELECT * FROM job_categories");
if ($results && $results->num_rows > 0) {
	while ($row = $results->fetch_assoc()) {
		$row["total_jobs"] = $c->query("SELECT * FROM jobs WHERE category_id=" . $row["id"])->num_rows;
		array_push($categories, $row);
	}
}
echo json_encode($categories);
