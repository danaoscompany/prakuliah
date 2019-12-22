<?php
include 'db.php';
$id = intval($_POST["id"]);
$c->query("UPDATE employers SET blocked=0 WHERE id=" . $id);
