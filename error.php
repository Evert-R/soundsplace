<?php
session_start();
require 'includes/connectdb.php';

/**
 * @author E.M. Rot
 * @copyright 2019
 */

$page_title = 'Ave-Book';
include('./includes/header.html');
?>

<div class="form">
    <h1>Error</h1>
	</BR>
    <p>
    <?php 
    if( isset($_SESSION['message']) AND !empty($_SESSION['message']) ): 
        echo '<div class="info">' . $_SESSION['message'] . '</div>';    
    else:
        header( "location: index.php" );
    endif;
    ?>
    </p>     
	</BR>
    <a href="index.php"><button class="button button-block"/>Home</button></a>
</div>

<?php
	include('./includes/footer.html');
?>