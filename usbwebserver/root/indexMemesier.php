<?php
   ob_start();
   session_start();
   include "db_connect.php";
?>

<?
   // error_reporting(E_ALL);
   // ini_set("display_errors", 1);
?>

<html lang = "en">

   <head>
      <title>Course Evaluations</title>
      <link href = "css/bootstrap.min.css" rel = "stylesheet">

      <style>
         body {
            padding-top: 40px;
            padding-bottom: 40px;
            background-color: #ADABAB;
            background-image:url(https://images4.alphacoders.com/236/thumb-1920-236784.jpg);background-repeat:no-repeat;background-size:cover;overflow-x:hidden;>
         }

         .form-signin {
            max-width: 330px;
            padding: 15px;
            margin: 0 auto;
            color: #017572;
         }

         .form-signin .form-signin-heading,
         .form-signin .checkbox {
            margin-bottom: 10px;
         }

         .form-signin .checkbox {
            font-weight: normal;
         }

         .form-signin .form-control {
            position: relative;
            height: auto;
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            box-sizing: border-box;
            padding: 10px;
            font-size: 16px;
         }

         .form-signin .form-control:focus {
            z-index: 2;
         }

         .form-signin input[type="email"] {
            margin-bottom: -1px;
            border-bottom-right-radius: 0;
            border-bottom-left-radius: 0;
            border-color:#017572;
         }

         .form-signin input[type="password"] {
            margin-bottom: 10px;
            border-top-left-radius: 0;
            border-top-right-radius: 0;
            border-color:#017572;
         }

         h2{
            text-align: center;
            color: #000000;
         }
      </style>

   </head>

   <body>
		<?php
		if(isset($_SESSION['valid'])){
			if(($_SESSION['valid']==true) ){
				header('Refresh: 0; URL = logout.php');
				die("<h2 align=\"center\">YOU ARE ALREADY LOGGED IN</h2><h3 align=\"center\">Logging out now</h3>");
			}
		}
		?>
      <h2 style="color:white;font-weight:bold;font-size:300%;">Please Login</h2>
      <div class = "container form-signin">

         <?php
            $msg = '';
            if (isset($_POST['login']) && !empty($_POST['username'])
               && !empty($_POST['password'])) {
				$username_in = $_POST['username'];
				$password_in = md5($_POST['password']);
				$sql = "SELECT * FROM login_info WHERE (username = '".$username_in."')";
				$result = $mysqli->query($sql) or die(mysqli_error($mysqli));	
				$row = $result->fetch_assoc();
				if($row["active"] == 1){
					$msg = 'User is already logged in';
					// if($_SESSION['valid']==true){
						// header('Refresh: 1; URL = logout.php');
						// die("<h2 align=\"center\">YOU ARE ALREADY LOGGED IN</h2><h3 align=\"center\">Logging out now</h3>");
					// }
				}
				else{
					$sql = "SELECT * FROM login_info WHERE (username = '".$username_in."') AND (password = '".$password_in."')";
					$result = $mysqli->query($sql) or die(mysqli_error($mysqli));	
					$row = $result->fetch_assoc();
				   if(($row["username"] == $username_in) && ($row["password"] == $password_in)) {
					  $_SESSION['valid'] = true;
					  $_SESSION['timeout'] = time();
					  $_SESSION['username'] = $_POST['username'];
						try{
							$mysqli->query("START TRANSACTION");
							$mysqli->query("UPDATE login_info SET active=1 WHERE username='".$username_in."' ");
							$mysqli->query("COMMIT");
						} catch(Exception $e){
							$mysqli->query("ROLLBACK");
							header('Refresh: 3; URL = logout.php');
							die("<h2 align=\"center\">ERROR WITH LOGGING IN</h2><h3 align=\"center\">Going back to login page</h3>");
						}
					  echo 'You have entered valid use name and password';
					  $mysqli->close();
					  if($row["user_type"] == "student"){
						header('Location: /evaluations.php');
					  }
					  elseif($row["user_type"] = "teacher"){
						  header('Location: /logout.php');
					  }
					  
				   }else {
					  $msg = 'Wrong username or password';
				   }
				}
            }
			$mysqli->close();
         ?>
      </div> <!-- /container -->

      <div class = "container">

         <form class = "form-signin" role = "form"
            action = "<?php echo htmlspecialchars($_SERVER['PHP_SELF']);
            ?>" method = "post">
            <h4 style="color:lime;font-weight:bold;"class = "form-signin-heading"><?php echo $msg; ?></h4>
            <input type = "text" class = "form-control"
               name = "username" placeholder = "Username"
               required autofocus></br>
            <input type = "password" class = "form-control"
               name = "password" placeholder = "Password" required>
            <button class = "btn btn-lg btn-primary btn-block" type = "submit"
               name = "login">Login</button>
         </form>

         <a href = "logout.php" tite = "Logout">Click here to clean Session.
		 <br><br><br><br><br><br><br><br><br><br><br><br><br><br>
	     <a  style="font-size:500%;background-color:red" href = "index.php" tite = "Logout">FFS GO BACK PLS!
      </div>

   </body>
</html>
