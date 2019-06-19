<?php
//load database parameters into $mysqli and start session
require './assets/includes/php/connectdb.php';
session_start();

// @author Evert Rot
// @copyright 2019

// set pagetitle and load the navigation-bar
$page_title = 'Soundspace - Log-in';
include('./assets/includes/html/header.html');

// check if a form was posted 
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	// Escape email to protect against SQL injections
	$email = $mysqli->escape_string($_POST['email']);
	$result = $mysqli->query("SELECT * FROM users WHERE email='$email'");

	// User login process, checks if user exists and password is correct
	if ($result->num_rows == 0) { // User doesn't exist
		$_SESSION['message'] = "User with that email doesn't exist!";
//		header("location: login.php");
	} else { // User exists
		$user = $result->fetch_assoc(); // 

		if (password_verify($_POST['password'], $user['password'])) {

			// password is ok, make session variables
			$_SESSION['email'] = $user['email'];
			$_SESSION['artist_name'] = $user['artist_name'];
			$_SESSION['real_name'] = $user['real_name'];
			$_SESSION['active'] = $user['active'];
			$_SESSION['id'] = $user['id'];
			// Set logged-in session-variable 
			$_SESSION['logged_in'] = true;

			// goto the timeline
			header("location: index.php");
		} else {
			$_SESSION['message'] = "You have entered wrong password, try again!";
//			header("location: login.php");
		}
	}
}


?>


<div class="blackout">
	<section class="content-wrapper content-wrapper-login">
		<form action="login.php" method="post" autocomplete="off">

			<div class="form-group">
				<label class="mr-sm-2">
					Email Address<span class="req">*</span>
				</label>
				<input class="form-control" type="email" required autocomplete="off" name="email" />
			</div>

			<div class="form-group">
				<label class="mr-sm-2">
					Password<span class="req">*</span>
				</label>
				<input class="form-control" type="password" required autocomplete="off" name="password" />
			</div>

			<div>
				<a href="forgot.php" class="text-secondary">Forgot Password?</a>
			</div>

			<button class="button button-block" name="login" />Log In</button>

		</form>
		<div>
			<?php
					if (isset($_SESSION['message'])) {
						echo "<p>" . $_SESSION['message'] . "</p>";
						unset($_SESSION['message']);
					}
					?>


		
		</div>
	</section>
</div>






<?php
include('./assets/includes/html/footer.html');
?>