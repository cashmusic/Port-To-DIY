<?php
$pagetitle = "CASH Music Dashboard";
$thispage = basename($_SERVER['PHP_SELF']);

session_start();
if ($_REQUEST['logout'] == 1) {
	$_SESSION['login'] = 0;
	if (isset($_COOKIE[session_name()])) {
		setcookie(session_name(), '', time()-42000, '/');
	}
	session_destroy();
} else {
	include_once('./path/to/core/core.php');
	$error_msg = "";
	if ($_SESSION['login'] == 1) {
		$currentLogin = 1;
		$which_stream = $_SESSION['ss_current'];
		$pagetitle = "Secure Stream Management";
	} else {
		$currentLogin = 0;
		if (isset($_REQUEST['login_form_s'])){
			$email = $_REQUEST['e'];			
			$pass = $_REQUEST['p'];
			$login_id = ss_verifyLogin($email,$pass);
			if ($login_id != false) {
				if (ss_getOrganizationsById($login_id) == false && ss_listAllAdminStreams($login_id) == false) {
					$error_msg = "<p>Sorry, you are not elligible for administration.</p>";
					$pagetitle = "CASH Music Dashboard";
				} else {
					$currentLogin = 1;
					$_SESSION['login'] = 1;
					$_SESSION['loginid'] = $login_id;
					$_SESSION['ss_current'] = 0;
					$pagetitle = "Secure Stream Management";
				}
			} else {
				$error_msg = "<p>Sorry, that email/password combination is not correct.</p>";
				$pagetitle = "CASH Music Dashboard";
			}
		}
	}
	if ($currentLogin == 1) {
		$allallowedstreams = ss_listAllUserAdminStreams($_SESSION['loginid']);
		$allorgs = ss_getOrganizationsById($_SESSION['loginid']);
		if ($allallowedstreams != false) {
			$allallowedstreamids = array();
			foreach ($allallowedstreams as $value) {
				$allallowedstreamids[] = $value['id'];
			}
		}
		if (isset($_REQUEST['which_stream']) && is_array($allallowedstreamids)){
			if (is_array($allorgs)) {
				if (in_array(1,$allorgs)) {$superadmin = true;}
			} else {
				$superadmin = false;
			}
			if (in_array($_REQUEST['which_stream'],$allallowedstreamids) || $superadmin) {
				$_SESSION['ss_current'] = $_REQUEST['which_stream'];
			} else {
				$_SESSION['ss_current'] = 0;
			}
			$which_stream = $_SESSION['ss_current'];
		}
		if (isset($_REQUEST['set_tag_login_id']) && isset($_REQUEST['set_tag_txt'])){
			$finaltag = trim($_REQUEST['set_tag_txt']);
			if ($finaltag != '' && $which_stream != 0) {
				ss_setTag($_REQUEST['set_tag_login_id'],$which_stream,$finaltag);
			}
		}

	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="css/main.css" rel="stylesheet" type="text/css" />
<script src="http://scripts.cashmusic.org/mootools/mootools.1.2b2.js" type="text/javascript"></script>
<script src="http://scripts.cashmusic.org/hg/hg_core.js" type="text/javascript" id="hg_core"></script>
<script src="scripts/hg_init.js" type="text/javascript"></script>
<script src="scripts/securestreams.js" type="text/javascript"></script>
<title>CASH Music: Dashboard</title>
</head>
<body>

<div id="mainspc">
	<div class="marginauto tmar1em">
		<img src="images/cashlogo.gif" width="36" height="36" style="position:relative;top:9px;margin-right:8px;" alt="CASH Music" /><h2 class="di"><?php echo $pagetitle ?></h2>
		<br /><br />
	</div>
	<div class="underlogo">
<?php

if (!$currentLogin) {
	?>
		<h2>Please Login:</h2>
		<?php echo $error_msg; ?>
		<p>
		Please enter your email address and password.	
		</p>
		<div class="tar">
			<form id="login_form" method="post" action="<?php echo basename($_SERVER['PHP_SELF']); ?>">
				<input type="hidden" name="login_form_s" value="1" />
				<span class="alt">email:</span> <input type="text" name="e" class="w160px" /><br />
				<span class="alt">password:</span> <input type="password" name="p" class="w160px" /><br /><br />
				<input type="submit" value=" log in " />
			</form>
		</div>
		
		</div>
		
	</body>
	</html>
<?php
	exit;
}
if ($which_stream == 0) {
	echo "<h2>Choose A Stream</h2><ul><big>";
	if ($allallowedstreams != false) {
		foreach ($allallowedstreams as $stream) {
			echo "<li>{$stream['artist_name']}: <a href=\"$thispage?which_stream={$stream['id']}\">{$stream['title']}</a></li>";
		}
	} else {
		echo "Sorry, it seems you are not permitted to administer any streams.";
	}
	echo "</big></ul>";
} else {
	if (!isset($_REQUEST['view'])) {
		$query = "SELECT l.id,s.total_logins,s.last_timestamp,l.first_name,l.last_name,l.email_address,l.organization FROM ss_permissions s JOIN ss_logins l ON s.login_id = l.id WHERE stream_id = $which_stream AND s.total_logins > 0 ORDER BY s.total_logins DESC, last_name ASC";
		$maintitle = "All Active Users";
	} else {
		$query = "SELECT l.id,s.total_logins,s.last_timestamp,l.first_name,l.last_name,l.email_address,l.organization FROM ss_permissions s JOIN ss_logins l ON s.login_id = l.id WHERE stream_id = $which_stream ORDER BY s.total_logins DESC, last_name ASC";
		$maintitle = "All Users";
	}
	if ($_POST['adduser']) {
		$email_address = $_POST['ea'];
		$user_pass = $_POST['pw'];
		$first_name = $_POST['fn'];
		$last_name = $_POST['ln'];
		$organization = $_POST['o'];
		$allowed_logins = $_POST['al'];
		$date_expires =  $_POST['de'];
		$added_user = false;
		if (empty($first_name) || empty($email_address) || empty($user_pass)) {
			echo "Please fill out at least the first name, email address, and password fields.";
		} else {
			if (empty($date_expires)) {
				$date_expires = -1;
			} else {
				$date_expires = strtotime($date_expires);
			}
			if (empty($allowed_logins)) {
				$allowed_logins = -1;
			}
			$added_user = addSecureStreamUser($email_address,$user_pass,$first_name,$last_name,$organization,$which_stream,$allowed_logins,$date_expires);
			if ($added_user) {
				echo "<h2>Success</h2><p>User added to the system. ($email_address)</p>";
			} else {
				$error_message = mysql_error();
				echo "<p>Error. $error_message</p>";
			}
		}
	}
	if ($_POST['search']) {
		if (trim($_POST['ss']) != '') {
			$finalsearch = trim($_POST['ss']);
			switch ($_POST['sb']) {
			case 1:
				$query = "SELECT l.id,s.total_logins,s.last_timestamp,l.first_name,l.last_name,l.email_address,l.organization FROM ss_permissions s JOIN ss_logins l, ss_tags t ON s.login_id = l.id AND t.login_id = l.id WHERE s.stream_id = $which_stream AND t.stream_id = $which_stream AND t.tag LIKE '%$finalsearch%' ORDER BY s.total_logins DESC, last_name ASC";				
				$maintitle = "Users Matching Tag: $finalsearch";
				break;
			case 2:
				$query = "SELECT l.id,s.total_logins,s.last_timestamp,l.first_name,l.last_name,l.email_address,l.organization FROM ss_permissions s JOIN ss_logins l ON s.login_id = l.id WHERE s.stream_id = $which_stream AND l.email_address LIKE '%$finalsearch%' ORDER BY s.total_logins DESC, last_name ASC";
				$maintitle = "Users Matching Email: $finalsearch";
				break;
			case 3:
				$query = "SELECT l.id,s.total_logins,s.last_timestamp,l.first_name,l.last_name,l.email_address,l.organization FROM ss_permissions s JOIN ss_logins l ON s.login_id = l.id WHERE s.stream_id = $which_stream AND (l.first_name LIKE '%$finalsearch%' OR l.last_name LIKE '%$finalsearch%') ORDER BY s.total_logins DESC, last_name ASC";
				$maintitle = "Users Matching Name: $finalsearch";
				break;
			case 4:
				$query = "SELECT l.id,s.total_logins,s.last_timestamp,l.first_name,l.last_name,l.email_address,l.organization FROM ss_permissions s JOIN ss_logins l ON s.login_id = l.id WHERE s.stream_id = $which_stream AND l.organization LIKE '%$finalsearch%' ORDER BY s.total_logins DESC, last_name ASC";
				$maintitle = "Users Matching Organization: $finalsearch";
				break;
			}
		}
	}
	$streaminfo = ss_getStreamInfo($which_stream);
	?>
	<div class="w66p fl">
		<h2 class="di"><?php echo $streaminfo['artist_name']; ?>: <span class="lighttxt"><?php echo $streaminfo['title']; ?></span></h2><br />
		<small><a href="<?php echo $streaminfo['primary_url']; ?>" class="external rmar2em">[view live site]</a> <a href="#" id="showaddover" class="rmar2em">[add new login]</a>
		<?php	
		if ($allallowedstreams != false && count($allallowedstreams) > 1) {
			echo "<a href=\"#\" id=\"showchangeover\" class=\"rmar2em\">[change stream]</a>";
	
		}
		echo "</small>";
		?>
	</div>
	<div class="tar w30p fr">
		<div class="tal marginalignright">
			<h2 class="alt">Search</h2>
			<form action="<?php echo basename($_SERVER['PHP_SELF']); ?>" method="post" class="margin0">
				<input type="text" name="ss" class="w140px bmar10px" /> by 
				<select name="sb" class="w80px">
					<option value="1">Tag</option>
					<option value="2">Email</option>
					<option value="3">Name</option>
					<option value="4">Organization</option>
				</select>
				<input type="hidden" name="search" value="1" />
				<input type="submit" value=" search " />
			</form>
		</div>
	</div>
	<div class="tmar3em cb">
		
		<?php
		$result = mysql_query($query,$dblink);
		if (mysql_num_rows($result)) { 
			$finaloutput = "";
			$drawercount = 1;
			$loginsused = 0;
			$totaluses = 0;
			$iffirst = ' firstdatarow';
			$ifzero = '';
			while ($row = mysql_fetch_assoc($result)) {
				if ($row['last_timestamp'] == 0) {
					$tmpdate = 'NA';
				} else {
					$tmpdate = date('D, M j \<\b\r \/\>\<\s\m\a\l\l\>(g:i A)\<\/\s\m\a\l\l\>',$row['last_timestamp']);
				}
				if ($row['total_logins'] == 0) {
					$ifzero = ' lighttxt';
				} else {
					$loginsused++;
					$totaluses += $row['total_logins'];
				}
				
				$currentuser = $row['id'];
				$currentusertag = ss_getTag($row['id'],$which_stream);
				
				$finaloutput .= "<div class=\"datarow$iffirst\">";
				$finaloutput .= "<div class=\"w5p fl tac\"><input type=\"checkbox\" name=\"datachk$drawercount\" /></div><div class=\"w5p fl tac$ifzero\">{$row['total_logins']}</div><div class=\"w14p fl tal$ifzero\">$tmpdate</div><div class=\"w33p fl tal$ifzero\">{$row['first_name']} {$row['last_name']}<br />{$row['email_address']}</div><div class=\"w33p fl tal$ifzero\">{$row['organization']}</div><div class=\"w9p fr tar\"><br /><small><a class=\"hg_drawertoggle ifjsdisplayi\" rev=\"drawer:target=datadetspc$drawercount,altLinkText=[less]\">[more]</a></small></div><div class=\"fixh\">&nbsp;</div>";
				$finaloutput .= "<div id=\"datadetspc$drawercount\" class=\"cb ifjsdisplaynone tmar10px\"><div class=\"det1col$ifzero\">address information unavailable</div><div class=\"det2col$ifzero\">Tags: <form id=\"tagform$drawercount\" action=\"$thispage\" method=\"post\" class=\"margin0 di\"><input type=\"hidden\" name=\"set_tag_login_id\" value=\"$currentuser\" /><input type=\"text\" name=\"set_tag_txt\" value=\"$currentusertag\" class=\"w160px\" /></form> <small><a href=\"javascript:document.getElementById('tagform$drawercount').submit();\">[save]</a></small></div><div class=\"fixh bmar10px\">&nbsp;</div></div>";
				$finaloutput .= "</div>\n";

				$drawercount++;
				$iffirst = '';
				$ifzero = '';
			} 
		} else {
			$finaloutput = "No Matching Users Found. Please try again.";
		}
		$totalusers = $drawercount-1;
		echo "<small><a href=\"" . basename($_SERVER['PHP_SELF']) . "\" class=\"rmar2em\">[view all active users]</a> <a href=\"" . basename($_SERVER['PHP_SELF']) . "?view=1\" class=\"rmar2em\">[view all users]</a></small><br /><br />";
		echo "<h2>$maintitle</h2>";
		if ($totalusers > 0) {
			if ($totalusers > 1) {
				$pluralornot = 's';
			} else {
				$pluralornot = '';
			}
			echo "<div class=\"lighttxt bmar1em\"><b>$totalusers user$pluralornot</b> found / <b>$loginsused</b> have viewed the stream ($totaluses total logins)</div>";
			echo "<div class=\"w5p fl tac\">&nbsp;</div><div class=\"w5p fl tac lighttxt\">Uses</div><div class=\"w14p fl tal lighttxt\">Last Login</div><div class=\"w33p fl tal lighttxt\">Name / Email</div><div class=\"w33p fl tal lighttxt\">Organization</div><div class=\"w9p fr tar\">&nbsp;</div><div class=\"fixh\">&nbsp;</div>";
		}
		echo $finaloutput;
		?>
		<div class="tar">
			<br /><a href="<?php echo basename($_SERVER['PHP_SELF']); ?>?logout=1">log out</a>&nbsp;
		</div>
	</div>
<?php
} 
?>
	
	<div>
</div>


<div id="addover" class="overlay">
	<div id="addoverbg" class="overlaybg"></div>
	<div id="addovercontent" class="overlaycontentspc">
		<h2 class="alt">Add A New User</h2>
		<p>
		<small class="lighttxt">(email, password, and first name required)</small>
		</p>
		<form action="<?php echo basename($_SERVER['PHP_SELF']); ?>" method="post" class="tar margin0">
			<b>email address: </b><input type="text" name="ea" class="w160px" /><br />
			<b>password: </b><input type="text" name="pw" class="w160px" /><br />
			<b>first name: </b><input type="text" name="fn" class="w160px" /><br />
			last name: <input type="text" name="ln" class="w160px" /><br />
			organization: <input type="text" name="o" class="w160px" /><br />
			<a class="hg_drawertoggle" rev="drawer:target=adduseralspace">limit total logins?</a><br />
			<small class="lighttxt">(default: unlimited)</small><br />
			<div id="adduseralspace" class="ifjsdisplaynone">
				allowed logins: <input type="text" name="al" class="w80px" /><br />
			</div>
			<a class="hg_drawertoggle" rev="drawer:target=adduserdespace">change expiration?</a><br />
			<small class="lighttxt">(default: 6 weeks / <?php echo date('m/d',(time()+3628800)) ?>)</small><br />
			<div id="adduserdespace" class="ifjsdisplaynone">
				date expires: <input type="text" name="de" value="<?php echo date('m/d/Y',(time()+3628800)) ?>" class="w80px" /><br />
			</div>
			<br />
			<input type="hidden" name="adduser" value="1" /> 
			<input type="submit" value=" add user " />
		</form> 
	</div>
</div>



<div id="changeover" class="overlay">
	<div id="changeoverbg" class="overlaybg"></div>
	<div id="changeovercontent" class="overlaycontentspc">
		<?php	
		if ($allallowedstreams != false && count($allallowedstreams) > 1) {
			echo "<h2 class=\"alt\">Change Stream</h2>";
			echo "<div id=\"changestreamspace\" class=\"margin0\"><ul>";
			$thispage = basename($_SERVER['PHP_SELF']);
			foreach ($allallowedstreams as $stream) {
				echo "<li><a href=\"$thispage?which_stream={$stream['id']}\">{$stream['artist_name']}: {$stream['title']}</a></li>";
			}
			echo "</ul>";
			echo "</div>";
		}
		?>
	</div>
</div>

	
</body>
</html>