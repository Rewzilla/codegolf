<?php

if(!defined("IN_MAIN"))
	error("Invalid access");

?>

		<pre><code><?php echo htmlentities(file_get_contents("golf.sh")); ?></code></pre>
		<a href="golf.sh">Download this script</a>