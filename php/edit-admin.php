<?php
include 'db.php';
$adminId = $_POST["id"];
$email = $_POST["email"];
$password = $_POST["password"];
$name = $_POST["name"];
$phone = $_POST["phone"];
$accepted = intval($_POST["verified"]);
$c->query("UPDATE admins SET email='" . $email . "', password='" . $password . "', name='" . $name . "', phone='" . $phone . "', accepted=" . $accepted . " WHERE id='" . $adminId . "'");
echo 0;