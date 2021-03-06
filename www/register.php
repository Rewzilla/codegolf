<?php

if(!defined("IN_MAIN"))
	error("Invalid access");

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

$type = "danger";
if(isset($_POST["submit"])) {

	$response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=" . $recaptcha_secret .
		"&response=" . $_POST["g-recaptcha-response"] . "&remoteip=" . $_SERVER['REMOTE_ADDR']);

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

		<?php if(isset($_POST["submit"])) { ?>
		<div class="alert alert-<?php echo $type; ?>" role="alert">
			<?php echo $msg; ?>
		</div>
		<?php } ?>
		<form method="POST">
			<div class="form-group">
				<label for="username">Username</label>
				<input type="text" class="form-control" id="username" name="username" placeholder="NextGenHacker101">
			</div>
			<div class="form-group">
				<label for="password">Password</label>
				<input type="password" class="form-control" id="password" name="password" placeholder="hunter2">
			</div>
			<div class="form-group">
				<label for="confirmpassword">Confirm Password</label>
				<input type="password" class="form-control" id="confirmpassword" name="confirmpassword" placeholder="hunter2">
			</div>
			<div class="form-group">
				<div class="g-recaptcha" data-sitekey="<?php echo $recaptcha_sitekey; ?>"></div>
			</div>
			<input type="hidden" name="submit" value="true">
			<button type="submit" class="btn btn-primary">Submit</button>
		</form>
