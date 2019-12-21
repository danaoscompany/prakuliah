<?php
include 'db.php';
$employerCount = intval($c->query("SELECT COUNT(*) AS total FROM employers")->fetch_assoc()["total"]);
$userCount = intval($c->query("SELECT COUNT(*) AS total FROM users")->fetch_assoc()["total"]);
$obj = array(
	'employers' => $employerCount,
	'users' => $userCount
);
echo json_encode($obj);
