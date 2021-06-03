<html>
<head>
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

<!-- jQuery library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

<!-- Latest compiled JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
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

date_default_timezone_set("America/New_York");

$errMsgCourse = $errMsgAction = $errMsgRoster = $errMsgRosterAdd = "";
$course_in = $action_in = "";
$roster_in = [];
$typeErr = 0;

if($_SERVER["REQUEST_METHOD"] == "POST"){
	if(empty($_POST["roster_in"])){
		$errMsgRoster = "CHOOSE STUDENT(S)";
	}
	if(empty($_POST["action_in"])){
		$errMsgAction = "CHOOSE AN ACTION";
	}
	if($_POST["course_in"] == "NULLCOURSE"){
		$errMsgCourse = "SELECT A COURSE";
	}
	if( (!empty($_POST["action_in"])) & (!empty($_POST["course_in"])) & (!empty($_POST["roster_in"])) ){
		$roster = $_POST["roster_in"];
		$action_Input = $_POST["action_in"];
		$course_Input = $_POST["course_in"];
		list($class_Input, $semester_Input) = explode("@@@@", $course_Input);
		
		try{
			$mysqli->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
			
			if($action_Input == "ADD"){
				for($i=0; $i<sizeof($roster);$i++){
					$netid = $roster[$i];
					$a = $mysqli->query("INSERT INTO course_rosters (Course_ID, semester, student_netid) VALUES('$class_Input', '$semester_Input', '$netid')");
					if(!$a){
						$errMsgRosterAdd = "Course roster sucessfully not added";
						throw new Exception();
					}
				}
				$errMsgRosterAdd = "Course roster sucessfully added";
				$typeErr = 1;
			}
			elseif($action_Input == "REMOVE"){
				for($i=0; $i<sizeof($roster);$i++){
					$netid = $roster[$i];
					$a = $mysqli->query("DELETE FROM course_rosters WHERE (Course_ID='$class_Input') AND (semester='$semester_Input') AND (student_netid='$netid')");
					if(!$a){
						$errMsgRosterAdd = "Course roster sucessfully not removed";
						throw new Exception();
					}
				}
				$errMsgRosterAdd = "Course roster sucessfully removed";
				$typeErr = 1;
			}
			$mysqli->commit();
			unset($_POST["roster_in"]);
			unset($_POST["course_in"]);
			unset($_POST["action_in"]);
		} catch(Exception $e){
			$mysqli->rollback();
			$errMsgRosterAdd = "Course roster request error";
			$typeErr = 2;
			unset($_POST["roster_in"]);
			unset($_POST["course_in"]);
			unset($_POST["action_in"]);
		}
		
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
if($typeErr == 2)
	echo "<h2 style=\"text-align:center;color:red\">$errMsgRosterAdd</h2>";
elseif($typeErr == 1)
	echo "<h2 style=\"text-align:center;color:green\">$errMsgRosterAdd</h2>";
?>
<form class="form-horizontal" method="post" action = "<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
<fieldset>

<!-- Form Name -->
<h1 align="center">Course Roster Change</h1>
<hr>

<?php 
$courseResult = $mysqli->query("SELECT * FROM courses");
echo "<h4 style=\"text-align:center;color:red\">$errMsgCourse</h4>";
?>
<div class="form-group">
  <label class="col-md-4 control-label" for="course_in">Select a Course</label>
  <div class="col-md-4">
		<select  class="js-example-basic-single form-control" name="course_in">
		  <option value = "NULLCOURSE"></option>
		  <?php
		  while($rowClass = $courseResult->fetch_assoc()){
			$row_in = $rowClass['Course_ID'] . "@@@@" . $rowClass['semester'];
			echo "<option value='" . $row_in .  "'>" . $rowClass['semester'] . " " . $rowClass['Course_ID']. " - " . $rowClass['name'] . "</option>";
		  }
		  ?>
		</select>
		<span class="help-block">Type a courseID or class name in the search bar for easier finding</span>
  </div>
</div>
<script>
$(document).ready(function() {
    $('.js-example-basic-single').select2();
});
</script>

<?php 
echo "<h4 style=\"text-align:center;color:red\">$errMsgRoster</h4>";
$studentsResult = $mysqli->query("SELECT * FROM students");
?>
<div class="form-group">
  <label class="col-md-4 control-label" for="roster_in[]">Course Roster</label>
  <div class="col-md-4">
		<select class="js-example-basic-multiple form-control" name="roster_in[]" multiple="multiple">
		  <?php
		  while($rowStudent = $studentsResult->fetch_assoc()){
			echo "<option value='" . $rowStudent['netid'] . "'>" . $rowStudent['netid'] . " - " . $rowStudent['last_name'] . ", " . $rowStudent['first_name'] . "</option>";
		  }
		  ?>
		</select>
		<span class="help-block">netid - [Last Name, First Name]<br>Type a name or netid in this input bar for easier finding
		<br>Selected students will become highlighted in the options
		<br>Reclicking highlighted students will also unselect them</span>
  </div>
</div>
<script>
$(document).ready(function() {
    $('.js-example-basic-multiple').select2();
});
</script>

<?php echo "<h4 style=\"text-align:center;color:red\">$errMsgAction</h4>";?>
<div class="form-group">
  <label class="col-md-4 control-label" for="action_in">Action</label>
  <div class="col-md-4">
  <div class="radio">
    <label for="action_in-0">
      <input type="radio" name="action_in" id="action_in-0" value="ADD"
	  <?php if(isset($_POST["action_in"])){if($_POST["action_in"]=="ADD"){echo"checked=\"checked\"";}}?>>
      ADD
    </label>
	</div>
  <div class="radio">
    <label for="action_in-1">
      <input type="radio" name="action_in" id="action_in-1" value="REMOVE"
	  <?php if(isset($_POST["action_in"])){if($_POST["action_in"]=="REMOVE"){echo"checked=\"checked\"";}}?>>
      REMOVE
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