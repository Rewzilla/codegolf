<?php

include("/path/to/config.php"); // [CHANGEME]

if($argc != 2)
	die("Usage: " . $argv[0] . " <users.csv>");

$db = new mysqli($mysql_host, $mysql_user, $mysql_pass, $mysql_db);
if(!$db)
	die("Database error");

$users = file($argv[1], FILE_IGNORE_NEW_LINES);
foreach($users as $user) {

	$user = explode(",", $user);
	$sql = $db->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
	$sql->bind_param("ss", $user[0], md5($user[1]));
	$sql->execute();
	$sql->close();

}


