<?php
include 'db.php';
$id = intval($_POST["id"]);
$c->query("UPDATE jobs SET available=1 WHERE id=" . $id);
