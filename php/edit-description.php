<?php
include 'db.php';
$role = $_POST["role"];
$id = intval($_POST["id"]);
$description = $_POST["description"];
if ($role == "employer") {
	$c->query("UPDATE employers SET biodata='" . $description . "' WHERE id=" . $id);
}
