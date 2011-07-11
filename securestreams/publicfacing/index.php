<?php
include_once('config.php');
include_once('classes/SecureStream.php');

session_cache_limiter('nocache');
header('P3P: CP="CAO PSA OUR"'); // IE privacy policy fix
ini_set('session.gc_maxlifetime', 9000);
ini_set('session.gc_probability', 1);
ini_set('session.gc_divisor', 100);
session_save_path(dirname(__FILE__) . './path/for/sessions');
session_start();

if ($_REQUEST['logout'] == 1) {
	$_SESSION['cash_ss_login'] = false;
	if (isset($_COOKIE[session_name()])) {
		setcookie(session_name(), '', time()-42000, '/');
	}
	session_destroy();
} else {
	if ($_SESSION['cash_ss_login']) {
		$ss = $_SESSION['ss'];
	}
	if (isset($_REQUEST['e']) && !$_SESSION['cash_ss_login']) {
		$email = $_REQUEST['e'];
		$pass = $_REQUEST['p'];

		$ss = new SecureStream(STREAM_ID,$email,$pass,$ss_playlist,INCLUDE_LOCATION);
		if ($ss->logged_in) {
			$_SESSION['cash_ss_login'] = true;
			$_SESSION['availableassets'] = 0;
			$_SESSION['ss'] = $ss;
		}
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"> 
<head> 
<title>CASH Music: <?php echo $ss_playlist['artist']; ?></title> 
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 

<link rel="icon" type="image/png" href="http://cashmusic.org/images/icons/cash.png" /> 

<script src="https://ajax.googleapis.com/ajax/libs/mootools/1.2.4/mootools-yui-compressed.js" type="text/javascript"></script> 
<script src="assets/scripts/flower/flower_core.js" type="text/javascript"></script> 
<script src="assets/scripts/securestream_flower_init.js" type="text/javascript"></script> 
 
<link href="assets/css/main.css" rel="stylesheet" type="text/css" /> 
</head> 
<body>
	
	<div id="mainspc">
	
		<h1 id="artisttitle"><?php echo $ss_playlist['artist']; ?></h1>
		<h2 id="releasetitle"><?php echo $ss_playlist['title']; ?></h2>

		<?php 
		if ($_SESSION['cash_ss_login']) { 
			echo '<div id="playerspc" class="flower_soundplayer">'
				. $ss->buildLinkString()
				. '</div>';
			
			echo '<div id="infospc">'
				. 'Logged in as ' . $ss->full_name . ' <span id="logout">(<a href="?logout=1">log out</a>)</span>'
				. $ss->buildLimitString()
				. '</div>';
		} else {
			?>
			<div id="loginspc">
				<div id="errorspc">
					<?php echo $ss->error_msg; ?>
				</div>
				<div id="formspc">
					<div>
						<form id="login_form" method="post" action="./">
							<label for="e">Your Email:</label>
							<input type="text" name="e" id="e" />

							<label for="p">Password:</label>
							<input type="password" name="p" id="p" />

							<input type="submit" class="button" value=" log in " />
						</form>
					<div id="helpspc">Need help? <a href="mailto:help@cashmusic.org">help@cashmusic.org</a></div>
				</div>
			</div>
			<?php
		}
		?>

	</dov>
 
</body> 
</html>