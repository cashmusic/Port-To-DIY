<?
session_cache_limiter('nocache');
header('P3P: CP="CAO PSA OUR"'); // IE privacy policy fix
ini_set('session.gc_maxlifetime', 9000);
ini_set('session.gc_probability', 1);
ini_set('session.gc_divisor', 100);
session_save_path(dirname(__FILE__) . './path/for/sessions');
session_start();
if ($_ENV['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' && $_SESSION['cash_ss_login'] == true) {
	if ($_POST['unlock'] == 1) {
		$_SESSION['availableassets'] = $_SESSION['availableassets']+1;
	}
	session_write_close();
	header("Pragma: no-cache");
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Cache-Control: private");
}
?>