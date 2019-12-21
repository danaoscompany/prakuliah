<?php
include 'db.php';
$start = intval($_POST["start"]);
$length = intval($_POST["length"]);
$results = $c->query("SELECT * FROM employers LIMIT " . $start . "," . $length);
$employers = [];
if ($results && $results->num_rows > 0) {
	while ($row = $results->fetch_assoc()) {
		array_push($employers, $row);
	}
}
echo json_encode($employers);
