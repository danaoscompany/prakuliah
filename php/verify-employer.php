<?php
include 'db.php';
$id = intval($_POST["id"]);
$sql = "UPDATE employers SET verified=1 WHERE id=" . $id;
$c->query($sql);
echo $sql;
