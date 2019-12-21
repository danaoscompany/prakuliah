<?php
include 'db.php';
$jobID = intval($_POST["id"]);
$c->query("UPDATE jobs SET available=2 WHERE id=" . $jobID);
