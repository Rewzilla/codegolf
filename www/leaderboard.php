<?php

if(!defined("IN_MAIN"))
	error("Invalid access");

/*
$sql = $db->prepare("SELECT s1.username, s1.score, s1.hash, s1.time FROM submissions s1 " .
		"INNER JOIN(" .
			"SELECT username, MIN(score) AS score, hash FROM submissions " .
			"GROUP BY username" .
		") s2 " .
		"ON s1.username = s2.username AND s1.score = s2.score AND s1.hash = s2.hash " .
		"ORDER BY score,time,username");
*/
$sql = $db->prepare(
	"SELECT username, score, hash, time FROM submissions " .
	"WHERE time IN (" .
		"SELECT MAX(time) " .
		"FROM submissions " .
		"GROUP BY username" .
	")" .
	"ORDER BY score, time, username;"
);
$sql->execute();
$sql->bind_result($username, $score, $hash, $time);

$place = 1;

?>

		<table class="table table-striped table-bordered">
			<tr><th>Place</th><th>Username</th><th>Best submission (bytes)</th><th>Fuzzy Hash</th><th>Time</th></tr>
			<?php while($sql->fetch()) {
				if($score == 9999) continue; ?>
				<tr><td><?php echo $place++; ?></td><td><?php echo $username; ?></td><td><?php echo $score; ?></td><td><?php echo $hash; ?></td><td><?php echo $time; ?></td></tr>
			<?php } ?>
		</table>
		<script>setTimeout("location.reload();", 10000);</script>
