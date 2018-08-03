<?php

define("IN_MAIN", true);

include("config.php");

// connect to the DB
$db = new mysqli($mysql_host, $mysql_user, $mysql_pass, $mysql_db);
if(!$db)
	error("Database error");

if(!isset($_GET["page"]) || !in_array($_GET["page"], $valid_pages)) {
	header("Location: /index.php?page=leaderboard");
	die("Error: Invalid page");
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
	<title>CodeGolf</title>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
	<!--<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>-->
	<script src='https://www.google.com/recaptcha/api.js'></script>
</head>

<body>
	<div class="container">
		<h1>CodeGolf</h1>
		<a href="index.php?page=leaderboard">Leaderboard</a> | <a href="index.php?page=scores">Score Trends</a> | <a href="index.php?page=register">Account Registration</a> | <a href="index.php?page=challenge">Current Challenge</a> | <a href="index.php?page=script">Submission Script</a>
		<hr/>
		<?php
		include($_GET["page"] . ".php");
		?>
		<hr/>
		Sourcecode available on <a href="https://github.com/Rewzilla/codegolf/" target="_blank">github</a> (GPLv3).
	</div>
</body>

</html>