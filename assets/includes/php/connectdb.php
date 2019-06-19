<?php

/* Database connection settings */
$host = 'localhost';
$user = 'Rotdesign';
$pass = 'Tijdelijk32?';
$db = 'trackbook';
$mysqli = new mysqli($host,$user,$pass,$db) or die($mysqli->error);


/**

// Connect met de rotmuziek database

define('DB_USER', 'Rotdesign');
define('DB_PASSWORD', 'VerhapselEmr23!');
define('DB_HOST', 'localhost');
define('DB_NAME', 'avebook');

$dbc = @mysqli_connect (DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) OR die('Could not connect to MySQL: ' . mysqli_connect_error() );

mysqli_set_charset($dbc, 'utf8');
**/
?>