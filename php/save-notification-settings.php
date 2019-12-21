<?php
include 'db.php';
$id = intval($_POST["id"]);
$newUser = intval($_POST["new_user_notification"]);
$applicationCancellation = intval($_POST["application_cancellation_notification"]);
$employeeAcceptance = intval($_POST["employee_acceptance_notification"]);
$c->query("UPDATE employers SET new_user_notification=" . $newUser . ", application_cancellation_notification=" . $applicationCancellation . ", employee_acceptance_notification=" . $employeeAcceptance . " WHERE id=" . $id);
