<?php

include("config.php");

// output an error message
function error($msg) {
	die("Error: " . $msg . "\n");
}

// save the score (maybe)
// output a success message
function success($username, $score) {
	global $db;
	$sql = $db->prepare("SELECT MIN(score) FROM submissions WHERE username=?");
	$sql->bind_param("s", $username);
	$sql->execute();
	$sql->bind_result($prev_score);
	$sql->fetch();
	$sql->close();

	if (is_null($prev_score) || $score < $prev_score) {
		$sql = $db->prepare("INSERT INTO submissions (username, time, score) VALUES (?,NOW(),?)");
		$sql->bind_param("si", $username, $score);
		$sql->execute();
		$sql->close();
	}
	die("Success: code was " . $score . " bytes\n");
}

// check to see if $user:$pass is valid and return $user if so
function get_user($user, $pass) {
	global $db;
	$sql = $db->prepare("SELECT username FROM users WHERE username=? AND password=?");
	$sql->bind_param("ss", $user, md5($pass));
	$sql->execute();
	$sql->bind_result($username);
	$sql->fetch();
	$sql->close();
	return $username;
}

// return a random testcase in the form...
// array(input, output)
function get_testcase() {
	global $db;
	$sql = $db->prepare("SELECT input, output FROM testcases ORDER BY RAND() LIMIT 0,1");
	$sql->execute();
	$sql->bind_result($input, $output);
	$sql->fetch();
	$sql->close();
	return array("input" => $input, "output" => $output);
}

// clean up files and folders on exit
function cleanup() {
	global $tmpdir;
	exec("rm -rf " . $tmpdir);
}

// return a random string
function tempname() {
	return md5(microtime() . getmypid());
}

// connect to the DB
$db = new mysqli($mysql_host, $mysql_user, $mysql_pass, $mysql_db);
if(!$db)
	error("Database error");

// make sure the user supplied authentication
if(!isset($_SERVER["PHP_AUTH_USER"]))
	error("Authentication required");

// check the authentication
if(($username = get_user($_SERVER["PHP_AUTH_USER"], $_SERVER["PHP_AUTH_PW"])) == false)
	error("Invalid username or password");

// make sure the user supplied a code file
if(!isset($_FILES["code"]))
	error("No file attached");

// create the temporary directory and move the code in
// make sure it gets cleaned up if/when the script/program ends
$tmpdir = $temp_dir . tempname();
register_shutdown_function("cleanup");
mkdir($tmpdir);
move_uploaded_file($_FILES["code"]["tmp_name"], $tmpdir . "/code.c");
file_put_contents($tmpdir . "/init.c", file_get_contents("seccomp.c"));

// compile the code
// ignore all warnings
$compile = shell_exec(
	"gcc -w -o " . $tmpdir . "/code " . $tmpdir . "/init.c " . $tmpdir . "/code.c -lseccomp 2>&1 " .
	"| grep -v '.o: In function'" .
	"| grep -v 'function is dangerous and should not be used'"
	);
$compile = str_replace($tmpdir, "/path/to", $compile);
if(!empty($compile))
	error($compile);

for ($i=0; $i<$num_tests; $i++) {

	// run the code
	$io = get_testcase();
	$descspec = array(
		0 => array("pipe", "r"),
		1 => array("pipe", "w"),
		2 => array("pipe", "w"),
	);
	$run = proc_open($runner, $descspec, $pipes, $tmpdir);
	if(!is_resource($run))
		error("Failed to run program");

	// write the input to the program's stdin
	// retreive the program's stdout and return value
	fwrite($pipes[0], $io["input"]);
	fclose($pipes[0]);
	$result = stream_get_contents($pipes[1]);
	$err = stream_get_contents($pipes[2]);
	fclose($pipes[1]);
	fclose($pipes[2]);
	$retval = proc_close($run);

	if(strpos($err, "Bad system call") !== false)
		error("No hax plz");

	// check if it got killed for hanging
	if($retval == 137)
		error("Program hung");

	// maybe show debug info
	if($allow_debug && strpos(file_get_contents($tmpdir . "/code.c"), "DEBUGPLZ") !== false) {
		echo "\n";
		echo "Your program output...   \n";
		echo "-------------------------\n";
		echo $result;
		echo "\n";
		echo "But I expected to see... \n";
		echo "-------------------------\n";
		echo $io["output"];
		echo "\n";
	}

	// check if the solution was wrong
	if($result != $io["output"])
		error("Incorrect solution");

}

// tell the user they got it right!
$size = filesize($tmpdir . "/code.c");
success($username, $size);
