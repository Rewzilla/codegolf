<?php

if(!defined("IN_MAIN"))
	error("Invalid access");

$sql = $db->prepare(
	"SELECT username, score, time FROM submissions " .
	"WHERE time IN (" .
		"SELECT MAX(time) " .
		"FROM submissions " .
		"GROUP BY username" .
	")" .
	"ORDER BY score, time, username;"
);
$sql->execute();
$sql->bind_result($username, $score, $time);

$place = 1;

?>

		<table class="table table-striped table-bordered">
			<tr><th>Place</th><th>Username</th><th>Best submission (bytes)</th><th>Time</th></tr>
			<?php while($sql->fetch()) {
				if($score == 9999) continue; ?>
				<tr><td><?php echo $place++; ?></td><td><?php echo $username; ?></td><td><?php echo $score; ?></td><td><?php echo $time; ?></td></tr>
			<?php } ?>
		</table>
		<script>setTimeout("location.reload();", 10000);</script>
