<?php
include 'db.php';
$accounts = [];
$results = $c->query("SELECT * FROM employers WHERE verified=1 AND blocked=0");
if ($results && $results->num_rows > 0) {
	while ($row = $results->fetch_assoc()) {
		array_push($accounts, $row);
	}
}
echo json_encode($accounts);
