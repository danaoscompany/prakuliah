<?php
include 'db.php';
$id = intval($_POST["id"]);
$c->query("UPDATE jobs SET available=0 WHERE id=" . $id);
