<?php
session_cache_limiter('nocache');
header('P3P: CP="CAO PSA OUR"'); // IE privacy policy fix
ini_set('session.gc_maxlifetime', 9000);
ini_set('session.gc_probability', 1);
ini_set('session.gc_divisor', 100);
session_save_path(dirname(__FILE__) . './path/for/sessions');
session_start();

if (isset($_GET['w']) && $_SESSION['availableassets'] > 0) {
	include_once('config.php');
	$whichtrack = $_GET['w'];

	// Instantiate S3 class, get uri
	require_once(S3_LIB_LOCATION);
	$s3 = new S3(awsAccessKey, awsSecretKey);
	$uri = $ss_playlist['data'][$whichtrack]['uri'];
	
	// Kill 'availableassets'
	$_SESSION['availableassets'] = 0;
	
	// Push to the file
	header("Location: " . S3::getAuthenticatedURL(S3_BUCKET, $uri, 999, false, true) );
} else {
	// No dice, redirect home
	header('Location: /');
}
?>