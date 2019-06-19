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
if ( $_SESSION['logged_in'] != 1 ) {
  $_SESSION['message'] = "You must log in before viewing the profile page.";
  header("location: login.php");  
	exit;
}

else {
    // set vriables from session
    $artist_name = $_SESSION['artist_name'];
}




$result = $mysqli->query("SELECT * FROM users WHERE artist_name='$artist_name'");
$user = $result->fetch_assoc();

$email = $user['email'];
$real_name = $user['real_name'];
$country = $user['country'];
$city = $user['city'];
$post_count = $user['post_count'];
$mb_used = $user['mb_used'];
$website = $user['website'];
$styles = $user['styles'];
$work_method = $user['work_method'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	
	$real_name = $mysqli->escape_string($_POST['realname']);
	$country = $mysqli->escape_string($_POST['country']);
	$city = $mysqli->escape_string($_POST['city']);
	$website = $mysqli->escape_string($_POST['website']);
	$styles = $mysqli->escape_string($_POST['styles']);
	$work_method = $mysqli->escape_string($_POST['workmethod']);
	
	
		$sql = "UPDATE users SET real_name = '$real_name', country = '$country', city = '$city', website = '$website', styles = '$styles', work_method = '$work_method' WHERE email='$email'";

					// Add user to the database
					if ( $mysqli->query($sql) ){
						$_SESSION['message'] = 'Settings updated!';
					}

					else {
						$_SESSION['message'] = 'Update failed!';
						header("location: error.php");
					}
}
?>


   
<div class = "main-wrap">
	<h1>Profile :</h1>
</div>
<div class = "main-wrap">
	<div class="form-inline form-wrap">
		
			<form action="profile.php" method="post" autocomplete="off">
          
          
            <div class="form-group">
              <label class="mr-sm-2">
                Artist Name :
              </label>
              <input type="text" value="<?php echo $artist_name; ?>" name='artistname' />
            </div>
			
			<div class="form-group">
              <label class="mr-sm-2">
                Real Name :
              </label>
              <input type="text" value="<?php echo $real_name; ?>" name='realname' />
            </div>
			
            <div class="form-group">
              <label class="mr-sm-2">
                E-mail :
              </label>
              <input type="text" value="<?php echo $email; ?>" name='email' />
            </div>
			
			<div class="form-group">
              <label class="mr-sm-2">
                Country :
              </label>
              <input type="text" value="<?php echo $country; ?>" name='country' />
            </div>
           
			<div class="form-group">
              <label class="mr-sm-2">
                City :
              </label>
              <input type="text" value="<?php echo $city; ?>" name='city' />
            </div>
			
			<div class="form-group">
              <label class="mr-sm-2">
                Website :
              </label>
              <input type="text" value="<?php echo $website; ?>" name='website' />
            </div>
			
			<div class="form-group">
              <label class="mr-sm-2">
                Styles :
              </label>
              <input type="text" value="<?php echo $styles; ?>" name='styles' />
            </div>
			
			<div class="form-group">
              <label class="mr-sm-2">
                Work method :
              </label>
              <input type="text" value="<?php echo $work_method; ?>" name='workmethod' />
            </div>
		   
		    <div>
				<a href="email.php" class="text-secondary" >Change e-mail adress</a>
			</div>
		   
		   
		   
          
          <button type="submit" class="button button-block" name="update" />Update</button>
          
          </form>
		
    </div> <!-- form-wrap -->
</div> <!-- main-wrap -->


<div class="main-wrap" id="message">
	<?php
	// display error and empty message variable
		if (isset($_SESSION['message'])) {
		echo "<p>".$_SESSION['message']."</p>";
		unset($_SESSION['message']);
		}
	?>
</div> <!-- main-wrap -->

<?php
if ($_SESSION['active'] != 1) {
	echo "<div class='main-wrap' id='message'>
				<p>Activate your account using the e-mail link to have full functionality</p>
			</div>";
}
?>

<?php
	include('./assets/includes/html/footer.html');
?>