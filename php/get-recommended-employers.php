<?php
include 'db.php';
$results = $c->query("SELECT * FROM employers WHERE recommended=1 AND blocked=0");
$users = [];
if ($results && $results->num_rows > 0) {
	while ($row = $results->fetch_assoc()) {
		array_push($users, $row);
	}
}
echo json_encode($users);
