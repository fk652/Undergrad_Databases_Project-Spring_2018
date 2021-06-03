<?php
	include "db_connect.php";
   session_start();
   	try{
		$mysqli->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
		//$mysqli->query("UPDATE login_info SET active=0 WHERE username='".$_SESSION["username"]."' ");
		if( !($stmt = $mysqli->prepare("UPDATE login_info SET active=0 WHERE username=? ")) )
			throw new Exception();
		$stmt->bind_param("s",$_SESSION["username"]);
		if( !($stmt->execute()) )
			throw new Exception();
		$stmt->close();
		$mysqli->query("COMMIT");
	} catch(Exception $e){
		$mysqli->query("ROLLBACK");
		header('Refresh: 3; URL = index.php');
		die("<h2 align=\"center\">ERROR WITH LOGGING OUT</h2><h3 align=\"center\">Going back to login page</h3>");
	}
	$mysqli->close();
   // unset($_SESSION["username"]);
   // unset($_SESSION["password"]);
   // unset($_SESSION['valid']);
   $_SESSION = array();
   if (ini_get("session.use_cookies")) {
		$params = session_get_cookie_params();
		setcookie(session_name(), '', time() - 42000,
			$params["path"], $params["domain"],
			$params["secure"], $params["httponly"]
		);
	}	
	session_unset();
	session_destroy();	
   echo 'You have cleaned session';
   header('Refresh: 0; URL = index.php');
?>
