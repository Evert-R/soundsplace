<?php
//load database parameters into $mysqli and start session
require './assets/includes/php/connectdb.php';
session_start();

// @author Evert Rot
// @copyright 2019

// set pagetitle and load the navigation-bar
$page_title = 'Soundsplace';
include('./assets/includes/html/header.html');

$track_audio = '';

// check if logged in, else goto login page
if ($_SESSION['logged_in'] != 1) {
	$_SESSION['message'] = "You must log in before making a post.";
	header("location: login.php");
	exit;
}
// Turn session vars in local vars
$artist_name = $_SESSION['artist_name'];
$email = $_SESSION['email'];
$active = $_SESSION['active'];
//	$track_audio = $_SESSION['track_audio'];


// read all info from user from database
$result = $mysqli->query("SELECT * FROM users WHERE artist_name='$artist_name'");
$user = $result->fetch_assoc();

$user_id = $user['id'];
$real_name = $user['real_name'];
$country = $user['country'];
$city = $user['city'];
$post_count = $user['post_count'];
$mb_used = $user['mb_used'];
$website = $user['website'];
$styles = $user['styles'];
$work_method = $user['work_method'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	// audio-file upload form handling
	//if ($_SERVER['REQUEST_METHOD'] == 'POST' and $_POST['formtype'] == 'audiofileupload') {
	if ($_POST['formtype'] == 'audiofileupload') {
		//if (isset($_POST['audiofileupload'])) {	
		if ($_FILES['the_file']['error'] > 0) {

			switch ($_FILES['the_file']['error']) {
				case 1:
				$_SESSION['message'] = 'File exceeded upload_max_filesize.';
					break;
				case 2:
				$_SESSION['message'] = 'File exceeded max_file_size.';
					break;
				case 3:
				$_SESSION['message'] = 'File only partially uploaded.';
					break;
				case 4:
				$_SESSION['message'] = 'No file uploaded.';
					break;
				case 6:
				$_SESSION['message'] = 'Cannot upload file: No temp directory specified.';
					break;
				case 7:
				$_SESSION['message'] = 'Upload failed: Cannot write to disk.';
					break;
				case 8:
				$_SESSION['message'] = 'A PHP extension blocked the file upload.';
					break;
			}
			header("location: makepost.php");
			exit;
		}

		/**  // Does the file have the right MIME type?
		  if ($_FILES['the_file']['type'] != 'image/png')
		  {
			echo 'Problem: file is not a PNG image.';
			exit;
		  }
		 */
		// put the file where we'd like it
		$uploaded_file = './uploads/' . $artist_name . round(microtime(true)) . $_FILES['the_file']['name'];

		if (is_uploaded_file($_FILES['the_file']['tmp_name'])) {
			if (!move_uploaded_file($_FILES['the_file']['tmp_name'], $uploaded_file)) {
				echo 'Problem: Could not move file to destination directory.';
				exit;
			}
		} else {
			echo 'Problem: Possible file upload attack. Filename: ';
			echo $_FILES['the_file']['name'];
			exit;
		}

		echo 'File uploaded successfully.';

		// show what was uploaded
		echo '<p>You uploaded the following image:<br/>';
		$track_audio = $uploaded_file;
		$_SESSION['track_audio'] = $track_audio;
		$_SESSION['track_size'] = ($_FILES['the_file']['size']);
		header("location: makepost.php");
		exit();
	}

	// image-file upload form handling
	//if ($_SERVER['REQUEST_METHOD'] == 'POST' and $_POST['formtype'] == 'imagefileupload') {
	//	if (isset($_POST['imagefileupload'])) {	
	if ($_POST['formtype'] == 'imagefileupload') {
		if ($_FILES['image_file']['error'] > 0) {
			echo 'Problem: ';
			switch ($_FILES['image_file']['error']) {
				case 1:
					echo 'File exceeded upload_max_filesize.';
					break;
				case 2:
					echo 'File exceeded max_file_size.';
					break;
				case 3:
					echo 'File only partially uploaded.';
					break;
				case 4:
					echo 'No file uploaded.';
					break;
				case 6:
					echo 'Cannot upload file: No temp directory specified.';
					break;
				case 7:
					echo 'Upload failed: Cannot write to disk.';
					break;
				case 8:
					echo 'A PHP extension blocked the file upload.';
					break;
			}
			exit;
		}

		/**  // Does the file have the right MIME type?
		  if ($_FILES['the_file']['type'] != 'image/png')
		  {
			echo 'Problem: file is not a PNG image.';
			exit;
		  }
		 */
		// put the file where we'd like it
		$uploaded_image = './uploads/' . $artist_name . round(microtime(true)) . $_FILES['image_file']['name'];

		if (is_uploaded_file($_FILES['image_file']['tmp_name'])) {
			if (!move_uploaded_file($_FILES['image_file']['tmp_name'], $uploaded_image)) {
				echo 'Problem: Could not move file to destination directory.';
				exit;
			}
		} else {
			echo 'Problem: Possible file upload attack. Filename: ';
			echo $_FILES['image_file']['name'];
			exit;
		}

		echo 'File uploaded successfully.';

		// show what was uploaded
		echo '<p>You uploaded the following image:<br/>';
		$track_image = $uploaded_image;
		$_SESSION['track_image'] = $track_image;
		header("location: makepost.php");
		exit();
	}


	// check if a track is uploaded, else goto upload page

	// message form handling






	if ($_SERVER['REQUEST_METHOD'] == 'POST' and $_POST['formtype'] == 'makepost') {

		if (!isset($_SESSION['track_audio'])) {
			$_SESSION['message'] = "You must select a track before makin' a post.";
			header("location: makepost.php");
			exit;
		}

		if (!isset($_SESSION['track_image'])) {
			$_SESSION['message'] = "You must select an image before makin' a post.";
			header("location: makepost.php");
			exit;
		}
		// set track variables from other forms session vars
		$track_audio = $_SESSION['track_audio'];
		$track_image = $_SESSION['track_image'];
		// get data from form
		$post_title = $mysqli->escape_string($_POST['posttitle']);
		$post_type = $mysqli->escape_string($_POST['posttype']);
		$post_format = $mysqli->escape_string($_POST['postformat']);
		$post_text = $mysqli->escape_string($_POST['posttext']);


		$soundfile_size = filesize($track_audio);
		// get sound duration using mp3file class
		require('./assets/includes/php/vendor/mp3file.class.php');
		$mp3file = new MP3File($track_audio); //http://www.npr.org/rss/podcast.php?id=510282
		//		$duration1 = $mp3file->getDurationEstimate();//(faster) for CBR only
		$duration2 = $mp3file->getDuration(); //(slower) for VBR (or CBR)
		//		echo "duration: $duration1 seconds"."\n";
		//		echo "estimate: $duration2 seconds"."\n";
		$soundfile_length = MP3File::formatTime($duration2);


		$_SESSION['post_title'] = $post_title;
		$_SESSION['post_type'] = $post_type;
		$_SESSION['post_format'] = $post_format;
		$_SESSION['post_text'] = $post_text;


		$sql = "INSERT INTO posts (user_id, post_title, track_audio, soundfile_size, soundfile_length, track_image, post_type, post_format, post_text)"
			. "VALUES ('$user_id', '$post_title', '$track_audio','$soundfile_size', '$soundfile_length', '$track_image', '$post_type','$post_format', '$post_text')";

		// Add post to the database
		if ($mysqli->query($sql)) {
			$_SESSION['message'] = 'You made your post!';
			unset($_SESSION['track_audio']);
			unset($_SESSION['track_image']);
		} else {
			$_SESSION['message'] = 'Update failed!';
			header("location: error.php");
		}
	}
}
?>


<div class="container">
	<header class="row">
		<div class="col-12">
			<div class="header-wrapper">
				<h1 class="header-text">Place a sound :</h1>
			</div>
		</div>
	</header>




	<article class="row">




		<section class="col-12 col-md-4">
			<div class="content-wrapper content-wrapper-yellow order-1">
				<form action="makepost.php" method="post" enctype="multipart/form-data">

					<input type="hidden" class="form-control" name="formtype" value="audiofileupload">
					<label for="the_file">
						<h5>Upload an audiofile:</h5>
					</label>

					<input type="file" class="form-control-file" name="the_file" value="" id="the_file" />
					<button type="submit" class="yellow-button" name="audiofileupload" value="Upload audiofile" data-toggle='tooltip' data-placement='bottom' data-delay='900' title='Click to upload selected sound'>
						<i class="fas fa-upload fa-3x"></i>
					</button>



				</form>
				</BR>
				<div class="message">
					<?php

					// display error and empty message variable
					if (isset($_SESSION['track_audio'])) {
						echo "File succesfully uploaded</br>";
						echo "Filesize: " . $_SESSION['track_size'] . "MB";
					}
					?>
				</div>
				<div>
					<i class='fas fa-play-circle fa-2x interface interface-play' data-toggle='tooltip' data-placement='bottom' data-delay='900' title='Play' onclick="wavesurfer.play()"></i>
					<i class='fas fa-pause-circle fa-2x interface interface-pause' data-toggle='tooltip' data-placement='bottom' data-delay='900' title='Pause' onclick="wavesurfer.pause()"></i>
				</div>
			</div>
		</section>

		<?php
		if (isset($_SESSION['track_audio'])) {
			echo "

            <section class='col-12 col-md-4 order-2 order-md-3'>
				<div class='content-wrapper content-wrapper-red'>
					<form action='makepost.php' method='post' enctype='multipart/form-data'>
										<input type='hidden' class='form-control' name='formtype' value='imagefileupload'>
										<label for='image_file'><h5>Upload an imagefile:</h5></label>
										<input type='file' class='form-control-file' name='image_file' value='' id='image_file'/>
										<input type='submit' class='btn btn-dark form-control red-button' name='imagefileupload' value='Upload imagefile'/>
							</form>
					</BR>
					<div class='message'>";

			// display error and empty message variable
			if (isset($_SESSION['track_image'])) {
				echo "Image succesfully uploaded";
				$track_image = $_SESSION['track_image'];
				echo "<div><img src='" . $track_image . " ' width='100%'></div>";
			}
			echo "
					</div>
				</div>
			</section>";
		}
		if (isset($_SESSION['track_audio'], $_SESSION['track_image'])) {
			echo "
			
            <section class='col-12 col-md-4 order-3 order-md-2'>
				<div class='content-wrapper content-wrapper-chalkboard'>									
					<form action='makepost.php' method='post' autocomplete='off'>
							<h5>Upload an imagefile:</h5>
							 <input type='hidden' name='formtype' value='makepost'>
								  
									<div class='form-group'>
									  <label class=''>
										Track Title :
									  </label>
									  <input class='form-control'  type='text' value='' name='posttitle' />
									</div>
									</br>
									
									<div class='form-group'>
									  <label for='posttype' class=''>
										Type :
									  </label>
									  <select class='form-control' multiple name='posttype'>
											<option>Track</option>
											<option>Liveset</option>
											<option></option>
											<option></option>
											<option></option>
									  </select>
									</div>
									
									<div class='form-check-inline'>
									  <label class='form-check-label'>
										  <input class='form-control'  type='radio' class='form-check-input' name='postformat'>MP3
									  </label>
									</div>
									<div class='form-check-inline'>
									  <label class='form-check-label'>
										  <input class='form-control'  type='radio' class='form-check-input' name='postformat'>FLAC
									  </label>
									</div>
									<div class='form-check-inline'>
									  <label class='form-check-label'>
										  <input class='form-control'  type='radio' class='form-check-input' name='postformat'>WAV
									  </label>
									</div>
									
									
									<div class='form-group'>
									  <label class=''></label>
									  <textarea class='form-control' rows='5' cols='25' value='' name='posttext' /></textarea> 
									</div>
								  <button type='submit' class='btn btn-dark' name='update' />Upload</button>
						</form>
				</div>
            </section>
		</article>";
		}
		?>


<div class="message">
	<?php
	// display error and empty message variable
		if (isset($_SESSION['message'])) {
		echo "<p>".$_SESSION['message']."</p>";
		unset($_SESSION['message']);
		}
  ?>
</div>

		</br></br></br></br>
</div>


</div>

<div class="container-fluid">
	<div class="row col-12">
		<footer class="footer fixed-bottom">
			<div class="waveformplayer" id="waveform"></div>
		</footer>
	</div>
</div>


<script src="https://unpkg.com/wavesurfer.js"></script>

<?php

// check for uploaded file and load the player
if (isset($_SESSION['track_audio'])) {
	echo "
				<script type='text/javascript'>
					var wavesurfer = WaveSurfer.create({
					container: '#waveform',
					waveColor: '#41c346',
					progressColor: '#dadada',
					height: '128'
				});

			wavesurfer.load('" . $_SESSION['track_audio'] . "');

			wavesurfer.on('ready', function () {
			
				});
				</script>";
}
?>


<?php

include('./assets/includes/html/footer.html');
?>