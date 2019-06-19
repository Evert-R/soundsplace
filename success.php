<?php
//load database parameters into $mysqli and start session
require './assets/includes/php/connectdb.php';
session_start();

// @author Evert Rot
// @copyright 2019

// set pagetitle and load the navigation-bar
$page_title = 'Ave-Book';
include('./assets/includes/html/header.html');
?>
<div class="form">
    <h1><?= 'Success'; ?></h1>
    <p>
    <?php 
    if( isset($_SESSION['message']) AND !empty($_SESSION['message']) ):
        echo $_SESSION['message'];    
    else:
        header( "location: index.php" );
    endif;
    ?>
    </p>
    <a href="index.php"><button class="button button-block"/>Home</button></a>
</div>

<?php
	include('./assets/includes/html/footer.html');
?>
