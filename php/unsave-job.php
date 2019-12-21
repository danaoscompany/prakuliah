<?php
include 'db.php';
$userID = intval($_POST["user_id"]);
$jobID = intval($_POST["job_id"]);
$c->query("DELETE FROM saved_jobs WHERE user_id=" . $userID . " AND job_id=" . $jobID);
