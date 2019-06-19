<?php
//load database parameters into $mysqli and start session
require './assets/includes/php/connectdb.php';
session_start();

// @author Evert Rot
// @copyright 2019

// set pagetitle and load the navigation-bar
$page_title = 'Track-Book';
include('./assets/includes/html/header.html');

// check if logged in, else goto login page
if ($_SESSION['logged_in'] != 1) {
	$_SESSION['message'] = "You must log-in first to log-out.";
	header("location: login.php");
	exit;
}

/* Log out process, unsets and destroys session variables */
session_unset();
session_destroy(); 
?>



<div class = "main-wrap">
	<h1>Logged out . . .</h1>
</div>
<div class = "main-wrap">
	<div class="form-inline form-wrap">

          <a href="login.php"><button class="button button-block"/>Go Again</button></a>
	</div>
</div>


<?php
	include('./assets/includes/html/footer.html');
?>