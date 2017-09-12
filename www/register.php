<?php

// maybe be in maintenance mode
include("maintenance.php");

include("config.php");

// connect to the DB
$db = new mysqli($mysql_host, $mysql_user, $mysql_pass, $mysql_db);
if(!$db)
	error("Database error");

// check if a user already exists
function user_exists($username) {
	global $db;
	$sql = $db->prepare("SELECT username FROM users WHERE username=?");
	$sql->bind_param("s", $username);
	$sql->execute();
	$sql->store_result();
	if($sql->num_rows > 0) {
		return true;
	} else {
		return false;
	}
}

$msg = "";
$type = "danger";
if(isset($_POST["submit"])) {

	$response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=" . $recaptcha_secret .
		"&response=" . $_POST['g-recaptcha-response'] . "&remoteip=" .$_SERVER['REMOTE_ADDR']);

	$captcha = json_decode($response, true)["success"];

	if(!$captcha) {

		$msg = "Error: Invalid CAPTCHA";

	} else if(empty($_POST["username"]) || empty($_POST["password"]) || empty($_POST["confirmpassword"])) {

		$msg = "Error: Required field(s) left blank";

	} else if ($_POST["password"] != $_POST["confirmpassword"]) {

		$msg = "Error: Passwords do not match";

	} else if (user_exists($_POST["username"])) {

		$msg = "Error: User already exists";

	} else {

		$username = htmlspecialchars($_POST["username"]);
		$password = md5($_POST["password"]);

		$sql = $db->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
		$sql->bind_param("ss", $username, $password);
		$sql->execute();
		$sql->close();

		$msg = "Success: User '". $username . "' created";
		$type = "success";

	}

}

?>

<!DOCTYPE html>
<html lang="en">

<head>
	<title>CodeGolf Registration</title>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
	<!--<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>-->
	<script src='https://www.google.com/recaptcha/api.js'></script>
</head>

<body>
	<div class="container">
		<h1>Register</h1>
		<a href="register.php">Account Registration</a> | <a href="challenge.txt" target="_blank">Current Challenge</a> | <a href="golf.sh" target="_blank">Submission Script</a>
		<hr/>
		<?php if(!empty($msg)) { ?>
		<div class="alert alert-<?php echo $type; ?>" role="alert">
			<?php echo $msg; ?>
		</div>
		<?php } ?>
		<form>
			<div class="form-group">
				<label for="username">Username</label>
				<input type="text" class="form-control" id="username" placeholder="NextGenHacker101">
			</div>
			<div class="form-group">
				<label for="password">Password</label>
				<input type="password" class="form-control" id="password" placeholder="hunter2">
			</div>
			<div class="form-group">
				<label for="confirmpassword">Confirm Password</label>
				<input type="password" class="form-control" id="confirmpassword" placeholder="hunter2">
			</div>
			<div class="form-group">
				<div class="g-recaptcha" data-sitekey="<?php echo $recaptcha_sitekey; ?>"></div>
			</div>
			<button type="submit" class="btn btn-primary" name="submit">Submit</button>
		</form>
	</div>
</body>

</html>