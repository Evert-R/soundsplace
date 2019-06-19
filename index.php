<?php
//load database parameters into $mysqli and start session
require './assets/includes/php/connectdb.php';
session_start();

// @author Evert Rot
// @copyright 2019

// set pagetitle and load the navigation-bar
$page_title = 'Soundspace';
$_SESSION['version'] = '0.1.0';
include('./assets/includes/html/header.html');

// check if logged in, else goto login page
if ($_SESSION['logged_in'] != 1) {
	$_SESSION['message'] = "You must log in before viewing the time line.";
	header("location: login.php");
	exit;
}

// set variables from sessions
$artist_name = $_SESSION['artist_name'];

?>

<div class="container">
	<header class="row">
		<div class="col-12">
			<div class="header-wrapper">
				<h1 class="header-text">Soundspace :</h1>
			</div>
		</div>
	</header>



	<?php
	// load class for mp3 parameters
	require('./assets/includes/php/vendor/mp3file.class.php');
	// Get posts from the database
	$timeline = $mysqli->query("SELECT * FROM posts");
	while ($row = mysqli_fetch_assoc($timeline)) {
		// make 3 columns per soundpost
		echo "
						<article class='row'>";
		echo "
							<section class='col-12 col-md-4  order-2 order-md-1'>
								<div class='content-wrapper content-wrapper-yellow'>";
		// left col info							
		echo "
									<div onclick=\"wavesurfer.load('" . $row['track_audio'] . "')\">" .
			$row['post_artist_name'] . "
									</div>
									<div onclick=\"wavesurfer.load('" . $row['track_audio'] . "')\"><h4>" .
			$row['post_title'] . "</h4>
									</div></br>
									<i class='fas fa-headphones fa-3x interface interface-load' data-toggle='tooltip' data-placement='bottom' data-delay='900' title='Load track into player' onclick=\"wavesurfer.load('" . $row['track_audio'] . "')\"></i>
									<i class='fas fa-play-circle fa-2x interface interface-play' data-toggle='tooltip' data-placement='bottom' data-delay='900' title='Play' onclick=\"wavesurfer.play()\"></i>
									<i class='fas fa-pause-circle fa-2x interface interface-pause' data-toggle='tooltip' data-placement='bottom' data-delay='900' title='Pause' onclick=\"wavesurfer.pause()\"></i>
									<a href='" . $row['track_audio'] . "' download>
										<i class='fas fa-download fa-2x  interface interface-download' data-toggle='tooltip' data-placement='bottom' data-delay='900' title='Download this track' onclick=\"wavesurfer.pause()\"></i>
									</a></br></br>
									<div>
										Uploaded " . $row['post_date'] . "
									</div>";
		// middle sound-picture
		echo "
								</div>
							</section>
							
							<section class='col-12 col-md-4 order-1 order-md-2'>
								<div class='content-wrapper content-wrapper-chalkboard'>";
		$post_artist_id = $row['user_id'];
		$result = $mysqli->query("SELECT * FROM users WHERE id='$post_artist_id'");
		$user = $result->fetch_assoc();
		$post_profile_image = $user['profile_image'];
		$post_artist_name = $row['post_artist_name'];

		echo "
												<div class='chat-wrapper chat-profile'>
													<div class='float-left'><img src='" . $post_profile_image . "' width='75' height='75'></div>
																
													<div class='chat-board'>" . $row['post_text'] . "</div>
												</div>
												<div class='chat-wrapper'>
													<div class='float-right'><img src='" . $post_profile_image . "' width='35' height='35'></div>				
													<div class='chat-board'>man wat is dit dik</div>
												</div>	
												<div class='chat-wrapper'>
													<div id='postdataright'><i class='fas fa-comment fa-2x' onclick=\"wavesurfer.load('" . $row['track_audio'] . "')\"></i></div>	
												</div>
								
									";
		echo "
								</div>
							</section>
							
							<section class='col-12 col-md-4  order-3 order-md-3'>
								<div class='content-wrapper content-wrapper-red'>";
		// right col get post user info
		echo "<div class='image-wrapper'><img src='" . $row['track_image'] . "' width='200px' height='200px' onclick=\"wavesurfer.load('" . $row['track_audio'] . "')\"></div>
								</div>
							</section>
						</BR>
						</article>
						";
	}
	?>




	<div class="main-wrap" id="message">
		<?php
		// display error and empty message variable
		if (isset($_SESSION['message'])) {
			echo "<p>" . $_SESSION['message'] . "</p>";
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
	<div class="main-wrap" id="signature">
		<p>
			<a href"https://evertrot.nl" target="blank">&copy; EVERT'S ROTDESIGN - 2019</a>
		</p>
	</div> <!-- main-wrap -->
	<BR><BR><BR>
	<div class="container-fluid>
	<div class="">
	<footer class=" footer fixed-bottom">
		<div class="waveformplayer" id="waveform"></div>
		</footer>
	</div>
</div>

<script src="https://unpkg.com/wavesurfer.js"></script>
<script>
	var wavesurfer = WaveSurfer.create({
		container: '#waveform',
		waveColor: '#41c346',
		progressColor: '#dadada',
		height: '128'
	});

	// wavesurfer.load('./uploads/jedi.mp3');

	wavesurfer.on('ready', function() {
		wavesurfer.play();
	});
</script>

<?php
include('./assets/includes/html/footer.html');
?>