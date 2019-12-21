<?php
include 'db.php';
$employerID = intval($_POST["id"]);
$fullName = $_POST["full_name"];
$address = $_POST["address"];
$phone = $_POST["phone"];
$whatsAppNumber = $_POST["whatsapp_number"];
$c->query("UPDATE employers SET full_name='" . $fullName . "', address='" . $address . "', phone='" . $phone . "', whatsapp_number='" . $whatsAppNumber . "' WHERE id=" . $employerID);
if (!file_exists('../userdata/profile_pictures')) {
    mkdir('../userdata/profile_pictures', 0777, true);
}
$profilePictureChanged = intval($_POST["profile_picture_changed"]);
if ($profilePictureChanged == 1) {
	move_uploaded_file($_FILES["file"]["tmp_name"], "../userdata/profile_pictures/" . $_FILES["file"]["name"]);
	$c->query("UPDATE employers SET card_picture='profile_pictures/" . $_FILES["file"]["name"] . "' WHERE id=" . $employerID);
}
