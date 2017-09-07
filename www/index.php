<?php

//die("Server is down for maintenance");

include("config.php") or die("config.php not found");

// connect to the DB
$db = new mysqli($mysql_host, $mysql_user, $mysql_pass, $mysql_db);
if(!$db)
	error("Database error");

$sql = $db->prepare("SELECT username, score FROM users ORDER BY score,username ASC");
$sql->execute();
$sql->bind_result($username, $score);

$place = 1;

?>

<!DOCTYPE html>
<html lang="en">

<head>
	<title>CodeGolf Leaderboard</title>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
	<!--<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>-->
	<meta http-equiv="refresh" content="10">
</head>

<body>
	<div class="container">
		<h1>CodeGolf Leaderboard</h1>
		View the current challenge <a href="challenge.txt" target="_blank">here</a>
		View working example code <a href="example.c" target="_blank">here</a>
		<hr/>
		<table class="table table-striped table-bordered">
			<tr><th>Place</th><th>Username</th><th>Best submission (bytes)</th></tr>
			<?php while($sql->fetch()) {
				if($score == 9999) continue; ?>
				<tr><td><?php echo $place++; ?></td><td><?php echo $username; ?></td><td><?php echo $score; ?></td></tr>
			<?php } ?>
		</table>
	</div>
</body>

</html>