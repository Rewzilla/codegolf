<?php

if(!defined("IN_MAIN"))
	error("Invalid access");

$sql = $db->prepare("SELECT s1.username, s1.score FROM submissions s1 " .
		"INNER JOIN(" .
			"SELECT username, MIN(score) AS score FROM submissions " .
			"GROUP BY username" .
		") s2 " .
		"ON s1.username = s2.username AND s1.score = s2.score " .
		"ORDER BY score,time,username");
$sql->execute();
$sql->bind_result($username, $score);

$place = 1;

?>

		<table class="table table-striped table-bordered">
			<tr><th>Place</th><th>Username</th><th>Best submission (bytes)</th></tr>
			<?php while($sql->fetch()) { ?>
				<tr><td><?php echo $place++; ?></td><td><?php echo $username; ?></td><td><?php echo $score; ?></td></tr>
			<?php } ?>
		</table>
		<script>setTimeout("location.reload();", 10000);</script>
