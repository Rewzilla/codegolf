<?php

if(!defined("IN_MAIN"))
	error("Invalid access");

?>

		<pre><code><?php echo htmlentities(file_get_contents("challenge.txt")); ?></code></pre>