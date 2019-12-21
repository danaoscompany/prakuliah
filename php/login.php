<?php
include 'db.php';
$email = $_POST["email"];
$password = $_POST["password"];
checkUsers($c, $email, $password);

function checkUsers($c, $email, $password) {
	$results = $c->query("SELECT * FROM users WHERE email='" . $email . "'");
	if ($results && $results->num_rows > 0) {
	    $row = $results->fetch_assoc();
	    if ($row["password"] != $password) {
	    	checkEmployers($c, $email, $password);
	    } else {
	    	echo 1;
	    }
	} else {
	    checkEmployers($c, $email, $password);
	}
}

function checkEmployers($c, $email, $password) {
	$results = $c->query("SELECT * FROM employers WHERE email='" . $email . "'");
	if ($results && $results->num_rows > 0) {
	    $row = $results->fetch_assoc();
	    if ($row["password"] != $password) {
            checkAdmins($c, $email, $password);
	    } else {
	    	echo 2;
	    }
	} else {
        checkAdmins($c, $email, $password);
	}
}

function checkAdmins($c, $email, $password) {
	$results = $c->query("SELECT * FROM admins WHERE email='" . $email . "'");
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
