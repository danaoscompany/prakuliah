<?php
include 'db.php';
$email = $_POST["email"];
$password = $_POST["password"];
checkUsers($email, $password);

function checkUsers($email, $password) {
	$results = $c->query("SELECT * FROM users WHERE email='" . $email . "'");
	if ($results && $results->num_rows > 0) {
	    $row = $results->fetch_assoc();
	    if ($row["password"] != $password) {
	    	checkEmployers($email, $password);
	    } else {
	    	echo 1;
	    }
	} else {
	    checkEmployers($email, $password);
	}
}

function checkEmployers($email, $password) {
	$results = $c->query("SELECT * FROM employers WHERE email='" . $email . "'");
	if ($results && $results->num_rows > 0) {
	    $row = $results->fetch_assoc();
	    if ($row["password"] != $password) {
	        checkPartners($email, $password);
	    } else {
	    	echo 2;
	    }
	} else {
	    checkPartners($email, $password);
	}
}

function checkPartners($email, $password) {
	$results = $c->query("SELECT * FROM partners WHERE email='" . $email . "'");
	if ($results && $results->num_rows > 0) {
	    $row = $results->fetch_assoc();
	    if ($row["password"] != $password) {
	        echo -1;
	    } else {
	    	echo 3;
	    }
	} else {
	    echo -1;
	}
}
