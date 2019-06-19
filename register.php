<?php
//load database parameters into $mysqli and start session
require './assets/includes/php/connectdb.php';
session_start();

// @author Evert Rot
// @copyright 2019

// set pagetitle and load the navigation-bar
$page_title = 'Track Book';
include('./assets/includes/html/header.html');

// check if a form was posted 
if ($_SERVER['REQUEST_METHOD'] == 'POST') {


		/* Registration process, inserts user info into the database 
		   and sends account confirmation email message
		 */

		// Set session variables

		$_SESSION['email'] = $_POST['email'];
		$_SESSION['artist_name'] = $_POST['artistname'];
		$_SESSION['real_name'] = $_POST['realname'];

		// Escape all $_POST variables to protect against SQL injections
		$artist_name = $mysqli->escape_string($_POST['artistname']);
		$real_name = $mysqli->escape_string($_POST['realname']);
		$email = $mysqli->escape_string($_POST['email']);
		$password = $mysqli->escape_string(password_hash($_POST['password'], PASSWORD_BCRYPT));
		$hash = $mysqli->escape_string( md5( rand(0,1000) ) );
			  
		// Check if user with that email already exists
		$result = $mysqli->query("SELECT * FROM users WHERE email='$email'") or die($mysqli->error());

		// We know user email exists if the rows returned are more than 0
		if ( $result->num_rows > 0 ) {
			
			$_SESSION['message'] = 'User with this email already exists!';
			header("location: error.php");
			
		}
		else { // Email doesn't already exist in a database, proceed...

			// active is 0 by DEFAULT (no need to include it here)
			$sql = "INSERT INTO users (artist_name, real_name, email, password, hash, joined) " 
					. "VALUES ('$artist_name','$real_name','$email','$password', '$hash', NOW())";

			// Add user to the database
			if ( $mysqli->query($sql) ){

				$_SESSION['active'] = 0; //0 until user activates their account with verify.php
				$_SESSION['logged_in'] = true; // So we know the user has logged in
				$_SESSION['message'] =
						
						 "Confirmation link has been sent to $email, please verify
						 your account by clicking on the link in the message!";

				// Send registration confirmation link (verify.php)
				$to      = $email;
				$subject = 'Trackbook Account Verification';
				$message_body = '
				Hello '.$artist_name.',

				Thank you for signing up to trackbook!
				A place where you can share your tracks with other artists.
				Please click this link to activate your account,
				or copy it to the adress bar of your browser :

				http://localhost/login-system/verify.php?email='.$email.'&hash='.$hash;  

				mail( $to, $subject, $message_body );

				header("location: index.php"); 

			}

			else {
				$_SESSION['message'] = 'Registration failed!';
				header("location: error.php");
			}

		}
}
?>


<div class = "main-wrap">
	<h1>Register :</h1>
</div>
<div class = "main-wrap">
		<div class="form-inline form-wrap">
				  
				  <form action="register.php" method="post" autocomplete="off">
				  
				  <div class="form-group">
					  <label class="mr-sm-2">
					
						Artist Name<span class="req">*</span>
					  </label>
					  <input type="text" required autocomplete="off" name='artistname' />
					</div>
				
					<div class="form-group">
					  <label class="mr-sm-2">
						Real Name<span class="req">*</span>
					  </label>
					  <input type="text"required autocomplete="off" name='realname' />
					</div>
				  

				  <div class="form-group">
					  <label class="mr-sm-2">
					  Email Address<span class="req">*</span>
					</label>
					<input type="email"required autocomplete="off" name='email' />
				  </div>
				  
				 <div class="form-group">
					  <label class="mr-sm-2">
					  Set A Password<span class="req">*</span>
					</label>
					<input type="password"required autocomplete="off" name='password'/>
				  </div>
				  
				  <button type="submit" class="button button-block" name="register" />Register</button>
				  
				  </form>

		</div>  
				
</div>


<?php
	include('./assets/includes/html/footer.html');
?>