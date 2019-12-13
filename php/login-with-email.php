<?php
include 'db.php';
$email = $_POST["email"];
checkUsers($c, $email);

function checkUsers($c, $email) {
	$results = $c->query("SELECT * FROM users WHERE email='" . $email . "'");
	if ($results && $results->num_rows > 0) {
	    $row = $results->fetch_assoc();
	    echo 1;
	} else {
	    checkEmployers($c, $email);
	}
}

function checkEmployers($c, $email) {
	$results = $c->query("SELECT * FROM employers WHERE email='" . $email . "'");
	if ($results && $results->num_rows > 0) {
	    $row = $results->fetch_assoc();
	    echo 2;
	} else {
	    checkPartners($c, $email);
	}
}

function checkPartners($c, $email) {
	$results = $c->query("SELECT * FROM partners WHERE email='" . $email . "'");
	if ($results && $results->num_rows > 0) {
	    $row = $results->fetch_assoc();
	    echo 3;
	} else {
	    echo -1;
	}
}
