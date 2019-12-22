<?php
include 'db.php';
$id = intval($_POST["id"]);
$c->query("UPDATE users SET blocked=0 WHERE id=" . $id);
