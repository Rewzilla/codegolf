<?php

if(!defined("IN_MAIN"))
	error("Invalid access");

$sql = $db->prepare("SELECT username, score FROM users ORDER BY score,time,username ASC");
$sql->execute();
$sql->bind_result($username, $score);

$place = 1;

?>

		<table class="table table-striped table-bordered">
			<tr><th>Place</th><th>Username</th><th>Best submission (bytes)</th></tr>
			<?php while($sql->fetch()) {
				if($score == 9999) continue; ?>
				<tr><td><?php echo $place++; ?></td><td><?php echo $username; ?></td><td><?php echo $score; ?></td></tr>
			<?php } ?>
		</table>
		<script>setTimeout("location.reload();", 10000);</script>