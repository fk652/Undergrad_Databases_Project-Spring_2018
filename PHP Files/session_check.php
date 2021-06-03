<?php
if((!isset($_SESSION['valid'])) || ($_SESSION['valid']==false)){
	header('Location: /index.php');
}
if (isset($_SESSION['timeout']) && (time() - $_SESSION['timeout'] > 1800)) {
    // last request was more than 30 minutes ago
    // session_unset();     // unset $_SESSION variable for the run-time 
    // session_destroy();   // destroy session data in storage
	header('Refresh: 1; URL = logout.php');
	die("<h2 align=\"center\">Session timed out</h2><h3 align=\"center\">Logging out now</h3>");
}
$_SESSION['timeout'] = time(); // update last activity time stamp
?>