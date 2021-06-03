<html>
<head>
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

<!-- jQuery library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

<!-- Latest compiled JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
body {
    font-family: "Lato", sans-serif;
}

.sidenav {
    height: 100%;
    width: 200px;
    position: fixed;
    z-index: 1;
    top: 0;
    left: 0;
    background-color: #111;
    overflow-x: hidden;
    padding-top: 20px;
}

.sidenav a {
    padding: 6px 6px 6px 32px;
    text-decoration: none;
    font-size: 25px;
    color: #818181;
    display: block;
}

.sidenav a:hover {
    color: #f1f1f1;
}

.main {
    margin-left: 200px; /* Same as the width of the sidenav */
}

@media screen and (max-height: 450px) {
  .sidenav {padding-top: 15px;}
  .sidenav a {font-size: 18px;}
}
</style>
</head>

<body style="background-image:url(http://adtecsol.com/images/website-background-images-tile-wallpaper-photo.jpg?crc=3942759990);background-size:auto;overflow-x:hidden;">
<!--<body style="background-color:#E6E6FA">-->
<!-- <h1>Course Evaluation <br><br> </hl> -->

<?php
session_start();
include "session_check.php";
if($_SESSION["type"] != "admin"){
	header('Refresh: 1; URL = logout.php');
	die("<h2 style=\"color:white;font-weight:bold;text-align:center;\">You are not an admin!</h2><h3 style=\"color:white;font-weight:bold;text-align:center;\">Logging out now</h3>");
}
include "db_connect.php";

$errMsgUser = $errMsgPass = $errMsgType = $errMsgFirst = $errMsgLast = $username_in = $password_in = $type_in = "";
$errMsgLogin = $errMsgInfo = $first_in = $last_in = $errMsgPassV = $passwordV_in = "";
$typeErr = $typeErr1 = 0;
$verified = $strongPass1 = $strongPass2 = $strongPass3 = $strongCheck = false;
if($_SERVER["REQUEST_METHOD"] == "POST"){
	if(empty($_POST["username_in"])){
		$errMsgUser = "NETID REQUIRED";
	}
	if(empty($_POST["password_in"])){
		$errMsgPass = "PASSWORD REQUIRED";
	}
	if(empty($_POST["type_in"])){
		$errMsgType = "SELECT A USER TYPE";
	}
	if(empty($_POST["last_in"])){
		$errMsgLast = "LAST NAME REQUIRED";
	}
	if(empty($_POST["first_in"])){
		$errMsgFirst = "FIRST NAME REQUIRED";
	}
	if(empty($_POST["passwordV_in"])){
		$errMsgPassV = "MUST VERIFY PASSWORD";
	}
	if((!empty($_POST["passwordV_in"])) & (!empty($_POST["password_in"]))){
		if($_POST["passwordV_in"] == $_POST["password_in"])
			$verified = true;
		else{
			$errMsgPassV = "PASSWORDS DID NOT MATCH";
			$errMsgPass = "PASSWORDS DID NOT MATCH";
			unset($_POST["password_in"]);
			unset($_POST["passwordV_in"]);
		}	
	}
	if($verified){
		if (strlen($_POST["password_in"]) < 8) {
			$errors[] = "Password too short!";
		}
		else
			$strongPass1 = true;
		if (!preg_match("#[0-9]+#", $_POST["password_in"])) {
			$errors[] = "Password must include at least one number!";
		}
		else
			$strongPass2 = true;
		if (!preg_match("#[a-zA-Z]+#", $_POST["password_in"])) {
			$errors[] = "Password must include at least one letter!";
		}   
		else
			$strongPass3 = true;
		if($strongPass3 & $strongPass2 & $strongPass1)
			$strongCheck = true;
		else{
			for($i=0; $i<sizeof($errors); $i++){
				$errMsgPass = ($errMsgPass."<br>".$errors[$i]);
			}
			unset($_POST["password_in"]);
			unset($_POST["passwordV_in"]);
		}
	}
	if( (!empty($_POST["username_in"])) & (!empty($_POST["password_in"])) & (!empty($_POST["type_in"])) & (!empty($_POST["last_in"]))
		& (!empty($_POST["first_in"]))	& (!empty($_POST["passwordV_in"])) & $verified & $strongCheck){
		$options = ['cost' => 12];
		$username_Input = $_POST["username_in"];
		$password_Input = password_hash($_POST["password_in"], PASSWORD_DEFAULT, $options);
		$type_Input = $_POST["type_in"];
		
		$last_Input = $_POST["last_in"];
		$first_Input = $_POST["first_in"];
		try{
			$mysqli->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
			if( !($stmt = $mysqli->prepare("SELECT * FROM login_info WHERE username=?")) )
				throw new Exception();
			$stmt->bind_param("s", $username_Input);
			if( !($stmt->execute()) )
				throw new Exception();
			$login_check = $stmt->get_result();
			$stmt->close();
			if($login_check->num_rows == 0){
				if( !($stmt = $mysqli->prepare("INSERT INTO login_info (username, password, user_type) VALUES(?, ?, ?)")) )
					throw new Exception();
				$stmt->bind_param("sss", $username_Input, $password_Input, $type_Input);
				if( !($stmt->execute()) )
					throw new Exception();
				$stmt->close();
				$errMsgLogin = "Login info succesfully added";
				$typeErr1 = 1;
			}
			else{
				$errMsgLogin = "There is already an account for this user";
				$typeErr1 = 2;
			}
			if($type_Input == "student"){
				if( !($stmt = $mysqli->prepare("SELECT * FROM students WHERE netid=?")) )
					throw new Exception();
				$stmt->bind_param("s", $username_Input);
				if( !($stmt->execute()) )
					throw new Exception();
				$student_check = $stmt->get_result(); 
				$stmt->close();
				if($student_check->num_rows == 0){
					if( !($stmt = $mysqli->prepare("INSERT INTO students (netid, first_name, last_name) VALUES(?, ?, ?)")) )
						throw new Exception();
					$stmt->bind_param("sss", $username_Input , $first_Input, $last_Input);
					if( !($stmt->execute()) )
						throw new Exception();
					$stmt->close();
					$errMsgInfo = "Succesfully added to students table";
					$typeErr = 1;
				}
				else{
					$errMsgInfo = "User is already added to students table";
					$typeErr = 2;
				}
			}
			elseif($type_Input == "teacher"){
				if( !($stmt = $mysqli->prepare("SELECT * FROM teachers WHERE netid=?")) )
					throw new Exception();
				$stmt->bind_param("s", $username_Input);
				if( !($stmt->execute()) )
					throw new Exception();
				$teacher_check = $stmt->get_result(); 
				$stmt->close();
				if($teacher_check->num_rows == 0){
					if( !($stmt = $mysqli->prepare("INSERT INTO teachers (netid, first_name, last_name) VALUES(?, ?, ?)")) )
						throw new Exception();
					$stmt->bind_param("sss", $username_Input, $first_Input, $last_Input);
					if( !($stmt->execute()) )
						throw new Exception();
					$stmt->close();
					$errMsgInfo = "Succesfully added to teachers table";
					$typeErr = 1;
				}
				else{
					$errMsgInfo = "User is already added to teachers table";
					$typeErr = 2;
				}
			}
			elseif($type_Input == "admin"){
				if( !($stmt = $mysqli->prepare("SELECT * FROM admins WHERE netid=?")) )
					throw new Exception();
				$stmt->bind_param("s", $username_Input);
				if( !($stmt->execute()) )
					throw new Exception();
				$admin_check = $stmt->get_result(); 
				$stmt->close();
				if($admin_check->num_rows == 0){
					if( !($stmt = $mysqli->prepare("INSERT INTO admins (netid, first_name, last_name) VALUES(?, ?, ?)")) )
						throw new Exception();
					$stmt->bind_param("sss", $username_Input, $first_Input, $last_Input);
					if( !($stmt->execute()) )
						throw new Exception();
					$stmt->close();
					$errMsgInfo = "Succesfully added to admin table";
					$typeErr = 1;
				}
				else{
					$errMsgInfo = "User is already added to admin table";
					$typeErr = 2;
				}
			}
			$mysqli->commit();
			unset($_POST["username_in"]);
			unset($_POST["password_in"]);
			unset($_POST["passwordV_in"]);
			unset($_POST["type_in"]);
			unset($_POST["last_in"]);
			unset($_POST["first_in"]);
		} catch(Exception $e){
			$mysqli->rollback();
			header('Refresh: 1; URL = registration.php');
			die("<h2 align=\"center\">ERROR WITH REGISTRATION</h2><h3 align=\"center\">Going back to registration page</h3>");
		}
		$mysqli->close();
		
	}
}
?>
<div class="sidenav">
  <a href="registration.php">Registration</a>
  <br>
  <a href="course_register.php">Course Registration</a>
  <br>
  <a href="course_roster.php">Course Roster Change</a>
  <br>
  <a href="logout.php">Logout</a>
</div>
<?php
if($typeErr1 == 2)
	echo "<h2 style=\"text-align:center;color:red\">$errMsgLogin</h2>";
elseif($typeErr1 == 1)
	echo "<h2 style=\"text-align:center;color:green\">$errMsgLogin</h2>";
if($typeErr == 2)
	echo "<h2 style=\"text-align:center;color:red\">$errMsgInfo</h2>";
elseif($typeErr == 1)
	echo "<h2 style=\"text-align:center;color:green\">$errMsgInfo</h2>";
?>
<form class="form-horizontal" method="post" action = "<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
<fieldset>

<!-- Form Name -->
<h1 align="center">Registration</h1>
<hr>

<?php echo "<h4 style=\"text-align:center;color:red\">$errMsgUser</h4>";?>
<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="username_in">netID</label>  
  <div class="col-md-4">
  <input id="username_in" name="username_in" type="text" placeholder="" class="form-control input-md" value="<?php echo isset($_POST["username_in"]) ? $_POST["username_in"] : ''; ?>">
  <span class="help-block">This will also be the username</span>  
  </div>
</div>

<!-- Password input
<?php //echo "<h4 style=\"text-align:center;color:red\">$errMsgPass</h4>";?>
<div class="form-group">
  <label class="col-md-4 control-label" for="password_in">Password</label>
  <div class="col-md-4">
    <input id="password_in" name="password_in" type="password" placeholder="" class="form-control input-md" value="<?php echo isset($_POST["password_in"]) ? $_POST["password_in"] : ''; ?>">
    <span class="help-block">any pass will do for now</span>
  </div>
</div>
-->

<?php echo "<h4 style=\"text-align:center;color:red\">$errMsgPass</h4>";?>
<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="password_in">Password</label>  
  <div class="col-md-4">
  <input id="password_in" name="password_in" type="password" placeholder="" class="form-control input-md" value="<?php echo isset($_POST["password_in"]) ? $_POST["password_in"] : ''; ?>">
  <span class="help-block">Password must be at least 8 characters long, contain at least one letter, and contain at least one number</span>  
  </div>
</div>

<?php echo "<h4 style=\"text-align:center;color:red\">$errMsgPassV</h4>";?>
<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="passwordV_in">Verify Password</label>  
  <div class="col-md-4">
  <input id="passwordV_in" name="passwordV_in" type="password" placeholder="" class="form-control input-md" value="<?php echo isset($_POST["passwordV_in"]) ? $_POST["passwordV_in"] : ''; ?>">
  <span class="help-block"></span>  
  </div>
</div>

<?php echo "<h4 style=\"text-align:center;color:red\">$errMsgFirst</h4>";?>
<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="first_in">First Name</label>  
  <div class="col-md-4">
  <input id="first_in" name="first_in" type="text" placeholder="" class="form-control input-md" value="<?php echo isset($_POST["first_in"]) ? $_POST["first_in"] : ''; ?>">
  <span class="help-block"></span>  
  </div>
</div>

<?php echo "<h4 style=\"text-align:center;color:red\">$errMsgLast</h4>";?>
<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="last_in">Last Name</label>  
  <div class="col-md-4">
  <input id="last_in" name="last_in" type="text" placeholder="" class="form-control input-md" value="<?php echo isset($_POST["last_in"]) ? $_POST["last_in"] : ''; ?>">
  <span class="help-block"></span>  
  </div>
</div>

<?php echo "<h4 style=\"text-align:center;color:red\">$errMsgType</h4>";?>
<!-- Multiple Radios -->
<div class="form-group">
  <label class="col-md-4 control-label" for="type_in">User Type</label>
  <div class="col-md-4">
  <div class="radio">
    <label for="type_in-0">
      <input type="radio" name="type_in" id="type_in-0" value="student"
	  <?php if(isset($_POST["type_in"])){if($_POST["type_in"]=="student"){echo"checked=\"checked\"";}}?>>
      student
    </label>
	</div>
  <div class="radio">
    <label for="type_in-1">
      <input type="radio" name="type_in" id="type_in-1" value="teacher"
	  <?php if(isset($_POST["type_in"])){if($_POST["type_in"]=="teacher"){echo"checked=\"checked\"";}}?>>
      teacher
    </label>
	</div>
	<div class="radio">
    <label for="type_in-1">
      <input type="radio" name="type_in" id="type_in-2" value="admin"
	  <?php if(isset($_POST["type_in"])){if($_POST["type_in"]=="admin"){echo"checked=\"checked\"";}}?>>
      admin
    </label>
	</div>
  </div>
</div>
<br>

<!-- Button -->
<div class="form-group">
  <label class="col-md-4 control-label" for="submit"></label>
  <div class="col-md-4">
    <button id="submit" name="submit" class="btn btn-primary">Submit</button>
  </div>
</div>

</fieldset>
</form>

</body>

</html>