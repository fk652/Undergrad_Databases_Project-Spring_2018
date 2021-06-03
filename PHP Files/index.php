<?php
   ob_start();
   session_start();
   include "db_connect.php";
?>

<?
   // error_reporting(E_ALL);
   // ini_set("display_errors", 1);
?>
<!DOCTYPE HTML>
<html lang = "en">

   <head>
      <title>Course Evaluations</title>
      <link href = "css/bootstrap.min.css" rel = "stylesheet">

      <style>
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

   <body style="background-image:url(http://wallpaperscraft.ru/image/zakat_priroda_nebo_svet_leto_84531_1920x1080.jpg);background-repeat:no-repeat;background-size:cover;overflow-x:hidden;">
		<?php
		if(isset($_SESSION['valid'])){
			if(($_SESSION['valid']==true) ){
				header('Refresh: 0; URL = logout.php');
				die("<h2 align=\"center\">YOU ARE ALREADY LOGGED IN</h2><h3 align=\"center\">Logging out now</h3>");
			}
		}
		?>
      <h2 style="color:white;font-weight:bold;font-size:250%;">Please Login</h2>
      <div class = "container form-signin">

         <?php
            $msg = '';
            if (isset($_POST['login']) && !empty($_POST['username'])
               && !empty($_POST['password'])) {
				$username_in = $_POST['username'];
				$password_in = $_POST['password'];
				$stmt = $mysqli->prepare("SELECT * FROM login_info WHERE (username = ?)");
				$stmt->bind_param("s",$username_in);
				$stmt->execute();
				$result = $stmt->get_result();
				$row = $result->fetch_assoc();
				$stmt->close();
				if($row["active"] == 1){
					$msg = 'User is already logged in';
					// if($_SESSION['valid']==true){
						// header('Refresh: 1; URL = logout.php');
						// die("<h2 align=\"center\">YOU ARE ALREADY LOGGED IN</h2><h3 align=\"center\">Logging out now</h3>");
					// }
				}
				else{
					$stmt = $mysqli->prepare("SELECT * FROM login_info WHERE (username = ?)");
					$stmt->bind_param("s",$username_in);
					$stmt->execute();
					$result = $stmt->get_result();
					$row = $result->fetch_assoc();
					$stmt->close();
				   if(($row["username"] == $username_in) && (password_verify($password_in,$row["password"])) ) {
					   if(password_needs_rehash($row["password"], PASSWORD_DEFAULT)){
						   $newPass = password_hash($password_in, PASSWORD_DEFAULT);
						   try{
								$mysqli->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
								if( !($mysqli->query("UPDATE login_info
												SET login_info.password = '$newPass'
												WHERE login_info.username = '$username_in'")) )
												throw new Exception();
								$mysqli->commit();
							} catch(Exception $e){
								$mysqli->rollback();
								header('Refresh: 3; URL = registration.php');
								die("<h2 align=\"center\">ERROR WITH REGISTRATION</h2><h3 align=\"center\">Going back to evaluation page</h3>");
							}
					   }
					  $_SESSION['valid'] = true;
					  $_SESSION['timeout'] = time();
					  $_SESSION['username'] = $_POST['username'];
					  $_SESSION['type'] = $row["user_type"];
						try{
							$mysqli->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
							if( !($stmt = $mysqli->prepare("UPDATE login_info SET active=1 WHERE username=? ")) )
								throw new Exception();
							$stmt->bind_param("s",$username_in);
							if( !($stmt->execute()) )
								throw new Exception();
							$stmt->close();
							$mysqli->commit();
						} catch(Exception $e){
							$mysqli->rollback();
							header('Refresh: 3; URL = logout.php');
							die("<h2 align=\"center\">ERROR WITH LOGGING IN</h2><h3 align=\"center\">Going back to login page</h3>");
						}
					  echo 'You have entered valid use name and password';
					  $mysqli->close();
					  if($row["user_type"] == "student"){
						header('Location: /evaluations.php');
					  }
					  elseif($row["user_type"] == "teacher"){
						  header('Location: /report.php');
					  }
					  elseif($row["user_type"] == "admin"){
						  header('Location: /registration.php');
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
      </div>

   </body>
</html>
