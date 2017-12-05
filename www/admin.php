<?php

include("config.php");

if(!isset($_SERVER["PHP_AUTH_USER"])) {
	header("HTTP/1.0 401 Unauthorized");
	header("WWW-Authenticate: Basic realm=\"Please Login\"");
	die("ERROR: Please refresh the page and login");
} else if($_SERVER["PHP_AUTH_USER"] != $admin_user || md5($_SERVER["PHP_AUTH_PW"]) != $admin_pass) {
	header("HTTP/1.0 401 Unauthorized");
	header("WWW-Authenticate: Basic realm=\"Invalid, try again\"");
	die("ERROR: Invalid password");
}

// connect to the DB
$db = new mysqli($mysql_host, $mysql_user, $mysql_pass, $mysql_db);
if(!$db)
	error("Database error");

if(isset($_POST["delete"])) {
	$sql = $db->prepare("DELETE FROM testcases WHERE ID=?");
	$sql->bind_param("i", $_POST["delete"]);
	$sql->execute();
	$sql->close();
}

if(isset($_POST["input"]) && isset($_POST["output"])) {
	$input = str_replace("\r\n", "\n", $_POST["input"]);
	$output = str_replace("\r\n", "\n", $_POST["output"]);
	$sql = $db->prepare("INSERT INTO testcases (input, output) VALUES (?, ?)");
	$sql->bind_param("ss", $input, $output);
	$sql->execute();
	$sql->close();
}

$sql = $db->prepare("SELECT * FROM testcases ORDER BY id ASC");
$sql->execute();
$sql->bind_result($id, $input, $output);

?>

<!DOCTYPE html>
<html lang="en">

<head>
	<title>Testcase Admin</title>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
	<!--<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>-->
	<script src='https://www.google.com/recaptcha/api.js'></script>
</head>

<body>
	<div class="container">
		<h1>Testcase Admin</h1>
		<hr/>
		<?php if(isset($_POST["delete"])) { ?>
		<div class="alert alert-success" role="alert">Testcase deleted</div>
		<?php } ?>
		<?php if(isset($_POST["input"]) && isset($_POST["output"])) { ?>
		<div class="alert alert-success" role="alert">Testcase added</div>
		<?php } ?>
		<table class="table table-striped table-bordered">
			<tr><th>Input</th><th>Output</th><th>Add/Delete</th></tr>
			<?php while($sql->fetch()) { ?>
				<tr><td><pre><?php echo $input; ?></pre></td><td><pre><?php echo $output; ?></pre></td><td style="text-align: center;"><form method="POST"><button type="submit" class="btn btn-danger" name="delete" value="<?php echo $id; ?>">Delete</button></form></td></tr>
			<?php } ?>
			<form method="POST">
			<div class="form-group">
				<tr><td><textarea class="form-control" rows="5" name="input"></textarea></td><td><textarea class="form-control" rows="5" name="output"></textarea></td><td style="text-align: center;"><button type="submit" class="btn btn-primary">Add</button></td></tr>
			</div>
			</form>
		</table>
		<hr/>
		Sourcecode available on <a href="https://github.com/Rewzilla/codegolf/" target="_blank">github</a> (GPLv3).
	</div>
</body>

</html>