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

$errMsgSemester = $errMsgYear = $errMsgCourseID = $errMsgCourseName = $errMsgRoom = $errMsgTeacher = "";
$errMsgEndT = $errMsgStartT = $errMsgWeek = $errMsgCourseAdd = $errMsgRosterAdd = "";
$semester_in = $courseID_in = $year_in = $courseName_in = $room_in = $teacher_in = "";
$typeErr = $typeErr1 = 0;
$roster_in = [];
$mon = $tues = $wed = $thurs = $fri = $sat = $sun = $timeConflict = false;
if($_SERVER["REQUEST_METHOD"] == "POST"){
	if(empty($_POST["semester_in"])){
		$errMsgSemester = "SELECT A SEMESTER";
	}
	if(empty($_POST["year_in"])){
		$errMsgYear = "SELECT A YEAR";
	}
	if(empty($_POST["courseID_in"])){
		$errMsgCourseID = "COURSE ID REQUIRED";
	}
	if(empty($_POST["courseName_in"])){
		$errMsgCourseName = "COURSE NAME REQUIRED";
	}
	if(empty($_POST["room_in"])){
		$errMsgRoom = "CLASS ROOM REQUIRED";
	}
	if(empty($_POST["endT_in"])){
		$errMsgEndT = "END TIME REQUIRED";
	}
	if(empty($_POST["startT_in"])){
		$errMsgStartT = "START TIME REQUIRED";
	}
	if($_POST["startT_in"] > $_POST["endT_in"]){
		$timeConflict = true;
		$errMsgStartT = "TIME CONFLICT (start time > end time)";
		$errMsgEndT = "TIME CONFLICT (start time > end time)";
		unset($_POST["endT_in"]);
		unset($_POST["startT_in"]);
	}
	if(empty($_POST["week_in"])){
		$errMsgWeek = "SELECT DAYS OF THE WEEK";
	}
	if($_POST["teacher_in"] == "NULLTEACHER"){
		$errMsgTeacher = "SELECT A TEACHER";
	}
	if( (!empty($_POST["semester_in"])) & (!empty($_POST["courseID_in"])) & (!empty($_POST["year_in"]))	
		& (!empty($_POST["courseName_in"])) & (!empty($_POST["room_in"])) & ($_POST["teacher_in"] != "NULLTEACHER")
		& (!empty($_POST["endT_in"])) & (!empty($_POST["startT_in"])) & (!empty($_POST["week_in"])) & (!$timeConflict) ){
		$semester_Input = $_POST["semester_in"];
		$year_Input = $_POST["year_in"];
		$courseID_Input = $_POST["courseID_in"];
		$courseName_Input = $_POST["courseName_in"];
		$room_Input = $_POST["room_in"];
		$endT_Input = $_POST["endT_in"];
		$startT_Input = $_POST["startT_in"];
		$week_Input = $_POST["week_in"];
		if(isset($_POST["roster_in"]))
			$roster = $_POST["roster_in"];
		$teacher_Input = $_POST["teacher_in"];
		if($semester_Input == "SPRING"){
			$start_date_Input = ($year_Input . "-01-22");
			$end_date_Input = ($year_Input . "-05-07");
		}
		elseif($semester_Input == "FALL"){
			$start_date_Input = ($year_Input . "-09-04");
			$end_date_Input = ($year_Input . "-12-07");
		}
		elseif($semester_Input == "SUMMER"){
			$start_date_Input = ($year_Input . "-05-21");
			$end_date_Input = ($year_Input . "-08-12");
		}
		elseif($semester_Input == "WINTER"){
			$start_date_Input = ($year_Input . "-01-02");
			$end_date_Input = ($year_Input . "-01-19");
		}
		$sem_year_Input = $semester_Input . " " . $year_Input;
		try{
			$mysqli->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
			
			if( !($stmt = $mysqli->prepare("SELECT * FROM courses WHERE (Course_ID=?) AND (semester='$sem_year_Input')")) )
				throw new Exception();
			$stmt->bind_param("s", $courseID_Input);
			if( !($stmt->execute()) )
				throw new Exception();
			$course_check = $stmt->get_result(); 
			$stmt->close();
			if($course_check->num_rows == 0){
				if( !($stmt = $mysqli->prepare("INSERT INTO courses (Course_ID, name, room, teacher_netid, semester, start_time, end_time, start_date, end_date) 
				 VALUES(?, ?, ?, '$teacher_Input', '$sem_year_Input', '$startT_Input', '$endT_Input', '$start_date_Input', '$end_date_Input')")) )
					throw new Exception();
				$stmt->bind_param("sss", $courseID_Input, $courseName_Input, $room_Input);
				if( !($stmt->execute()) )
					throw new Exception();
				$stmt->close();
				for($i=0; $i<sizeof($week_Input); $i++){
					if($week_Input[$i] == "mon"){
						if( !($stmt = $mysqli->prepare("UPDATE courses SET courses.mon='1' WHERE (courses.Course_ID=?) AND (courses.semester='$sem_year_Input')")) )
							throw new Exception();
						$stmt->bind_param("s", $courseID_Input);
						if( !($stmt->execute()) )
							throw new Exception();
						$stmt->close();
					}
					elseif($week_Input[$i] == "tues"){
						if( !($stmt = $mysqli->prepare("UPDATE courses SET courses.tues='1' WHERE (courses.Course_ID=?) AND (courses.semester='$sem_year_Input')")) )
							throw new Exception();
						$stmt->bind_param("s", $courseID_Input);
						if( !($stmt->execute()) )
							throw new Exception();
						$stmt->close();
					}
					elseif($week_Input[$i] == "wed"){
						if( !($stmt = $mysqli->prepare("UPDATE courses SET courses.wed='1' WHERE (courses.Course_ID=?) AND (courses.semester='$sem_year_Input')")) )
							throw new Exception();
						$stmt->bind_param("s", $courseID_Input);
						if( !($stmt->execute()) )
							throw new Exception();
						$stmt->close();
					}
					elseif($week_Input[$i] == "thurs"){
						if( !($stmt = $mysqli->prepare("UPDATE courses SET courses.thurs='1' WHERE (courses.Course_ID=?) AND (courses.semester='$sem_year_Input')")) )
							throw new Exception();
						$stmt->bind_param("s", $courseID_Input);
						if( !($stmt->execute()) )
							throw new Exception();
						$stmt->close();
					}
					elseif($week_Input[$i] == "fri"){
						if( !($stmt = $mysqli->prepare("UPDATE courses SET courses.fri='1' WHERE (courses.Course_ID=?) AND (courses.semester='$sem_year_Input')")) )
							throw new Exception();
						$stmt->bind_param("s", $courseID_Input);
						if( !($stmt->execute()) )
							throw new Exception();
						$stmt->close();
					}
					elseif($week_Input[$i] == "sat"){
						if( !($stmt = $mysqli->prepare("UPDATE courses SET courses.sat='1' WHERE (courses.Course_ID=?) AND (courses.semester='$sem_year_Input')")) )
							throw new Exception();
						$stmt->bind_param("s", $courseID_Input);
						if( !($stmt->execute()) )
							throw new Exception();
						$stmt->close();
					}
					elseif($week_Input[$i] == "sun"){
						if( !($stmt = $mysqli->prepare("UPDATE courses SET courses.sun='1' WHERE (courses.Course_ID=?) AND (courses.semester='$sem_year_Input')")) )
							throw new Exception();
						$stmt->bind_param("s", $courseID_Input);
						if( !($stmt->execute()) )
							throw new Exception();
						$stmt->close();
					}
				}
				$errMsgCourseAdd = "Course info succesfully added";
				$typeErr = 1;
			}
			else{
				$errMsgCourseAdd = "Course has already been registered";
				$typeErr = 2;
			}
			if(!empty($roster)){
				for($i=0; $i<sizeof($roster);$i++){
					$netid = $roster[$i];
					if( !($stmt = $mysqli->prepare("INSERT INTO course_rosters (Course_ID, semester, student_netid) VALUES(?, '$sem_year_Input', '$netid')")) )
						throw new Exception();
					$stmt->bind_param("s", $courseID_Input);
					if( !($stmt->execute()) )
						throw new Exception();
					$stmt->close();
				}
				$errMsgRosterAdd = "Course roster sucessfully added";
				$typeErr1 = 1;
			}
			$mysqli->commit();
			unset($_POST["semester_in"]);
			unset($_POST["year_in"]);
			unset($_POST["courseID_in"]);
			unset($_POST["courseName_in"]);
			unset($_POST["teacher_in"]);
			unset($_POST["startT_in"]);
			unset($_POST["endT_in"]);
			unset($_POST["week_in"]);
			unset($_POST["room_in"]);
			unset($_POST["roster_in"]);
		} catch(Exception $e){
			$mysqli->rollback();
			header('Refresh: 3; URL = course_registration.php');
			die("<h2 align=\"center\">ERROR WITH REGISTRATION</h2><h3 align=\"center\">Going back to registration page</h3>");
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
	echo "<h2 style=\"text-align:center;color:red\">$errMsgCourseAdd</h2>";
elseif($typeErr == 1)
	echo "<h2 style=\"text-align:center;color:green\">$errMsgCourseAdd</h2>";
if($typeErr1 == 1)
	echo "<h2 style=\"text-align:center;color:green\">$errMsgRosterAdd</h2>";
?>
<form class="form-horizontal" method="post" action = "<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
<fieldset>

<!-- Form Name -->
<h1 align="center">Course Registration</h1>
<hr>

<?php echo "<h4 style=\"text-align:center;color:red\">$errMsgCourseID</h4>";?>
<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="courseID_in">Course ID</label>  
  <div class="col-md-4">
  <input id="courseID_in" name="courseID_in" type="text" placeholder="" class="form-control input-md" value="<?php echo isset($_POST["courseID_in"]) ? $_POST["courseID_in"] : ''; ?>">
  <span class="help-block">include the section number at the end of the ID</span>  
  </div>
</div>

<?php echo "<h4 style=\"text-align:center;color:red\">$errMsgCourseName</h4>";?>
<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="courseName_in">Course Name</label>  
  <div class="col-md-4">
  <input id="courseName_in" name="courseName_in" type="text" placeholder="" class="form-control input-md" value="<?php echo isset($_POST["courseName_in"]) ? $_POST["courseName_in"] : ''; ?>">
  <span class="help-block"></span>  
  </div>
</div>

<?php echo "<h4 style=\"text-align:center;color:red\">$errMsgRoom</h4>";?>
<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="room_in">Class Room</label>  
  <div class="col-md-4">
  <input id="room_in" name="room_in" type="text" placeholder="" class="form-control input-md" value="<?php echo isset($_POST["room_in"]) ? $_POST["room_in"] : ''; ?>">
  <span class="help-block">Same room number/name as shown on NYU Albert<br>Enter NONE if room isn't given</span>
  </div>
</div>

<?php echo "<h4 style=\"text-align:center;color:red\">$errMsgSemester</h4>";?>
<!-- Multiple Radios -->
<div class="form-group">
  <label class="col-md-4 control-label" for="semester_in">Semester</label>
  <div class="col-md-4">
  <div class="radio">
    <label for="type_in-0">
      <input type="radio" name="semester_in" id="semester_in-0" value="SPRING" 
	  <?php if(isset($_POST["semester_in"])){if($_POST["semester_in"]=="SPRING"){echo"checked=\"checked\"";}}?>>
      SPRING
    </label>
	</div>
  <div class="radio">
    <label for="type_in-1">
      <input type="radio" name="semester_in" id="semester_in-1" value="FALL"
	  <?php if(isset($_POST["semester_in"])){if($_POST["semester_in"]=="FALL"){echo"checked=\"checked\"";}}?>>
      FALL
    </label>
	</div>
	<div class="radio">
    <label for="type_in-1">
      <input type="radio" name="semester_in" id="semester_in-2" value="SUMMER"
	  <?php if(isset($_POST["semester_in"])){if($_POST["semester_in"]=="SUMMER"){echo"checked=\"checked\"";}}?>>
      SUMMER
    </label>
	</div>
		<div class="radio">
    <label for="type_in-1">
      <input type="radio" name="semester_in" id="semester_in-3" value="WINTER"
	  <?php if(isset($_POST["semester_in"])){if($_POST["semester_in"]=="WINTER"){echo"checked=\"checked\"";}}?>>
      WINTER
    </label>
	</div>
  </div>
</div>

<?php echo "<h4 style=\"text-align:center;color:red\">$errMsgStartT</h4>";?>
<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="startT_in">Start Time</label>  
  <div class="col-md-2">
  <input id="startT_in" name="startT_in" type="time" class="form-control input-md" value="<?php echo isset($_POST["startT_in"]) ? $_POST["startT_in"] : ''; ?>">
  <span class="help-block">12 HR Format</span>  
  </div>
</div>

<?php echo "<h4 style=\"text-align:center;color:red\">$errMsgEndT</h4>";?>
<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="endT_in">End Time</label>  
  <div class="col-md-2">
  <input id="endT_in" name="endT_in" type="time" class="form-control input-md" value="<?php echo isset($_POST["endT_in"]) ? $_POST["endT_in"] : ''; ?>">
  <span class="help-block">12 HR Format</span>  
  </div>
</div>

<?php echo "<h4 style=\"text-align:center;color:red\">$errMsgWeek</h4>";?>
<!-- Multiple Checkboxes -->
<div class="form-group">
  <label class="col-md-4 control-label" for="week_in[]">Days of the Week</label>
  <div class="col-md-4">
  <div class="checkbox">
    <label for="week_in-0">
      <input type="checkbox" name="week_in[]" id="week_in-0" value="mon">
      Monday
    </label>
	</div>
  <div class="checkbox">
    <label for="week_in-1">
      <input type="checkbox" name="week_in[]" id="week_in-1" value="tues">
      Tuesday
    </label>
	</div>
  <div class="checkbox">
    <label for="week_in-2">
      <input type="checkbox" name="week_in[]" id="week_in-2" value="wed">
      Wednesday
    </label>
	</div>
  <div class="checkbox">
    <label for="week_in-3">
      <input type="checkbox" name="week_in[]" id="week_in-3" value="thurs">
      Thursday
    </label>
	</div>
  <div class="checkbox">
    <label for="week_in-4">
      <input type="checkbox" name="week_in[]" id="week_in-4" value="fri">
      Friday
    </label>
	</div>
  <div class="checkbox">
    <label for="week_in-5">
      <input type="checkbox" name="week_in[]" id="week_in-5" value="sat">
      Saturday
    </label>
	</div>
  <div class="checkbox">
    <label for="week_in-6">
      <input type="checkbox" name="week_in[]" id="week_in-6" value="sun">
      Sunday
    </label>
	</div>
  </div>
</div>

<?php 
$yearCurrent = date("Y");
$yearPast = date("Y",strtotime('-1 year'));
$yearNext = date("Y",strtotime('+1 year'));
echo "<h4 style=\"text-align:center;color:red\">$errMsgYear</h4>";
?>
<div class="form-group">
  <label class="col-md-4 control-label" for="year_in">Year</label>
  <div class="col-md-4">
  <div class="radio">
    <label for="type_in-0">
      <input type="radio" name="year_in" id="year_in-0" value="<?php echo"$yearPast"; ?>"
	  <?php if(isset($_POST["year_in"])){if($_POST["year_in"]==$yearPast){echo"checked=\"checked\"";}}?>>
      <?php echo"$yearPast"; ?>
    </label>
	</div>
  <div class="radio">
    <label for="type_in-1">
      <input type="radio" name="year_in" id="year_in-1" value="<?php echo"$yearCurrent"; ?>"
	  <?php if(isset($_POST["year_in"])){if($_POST["year_in"]==$yearCurrent){echo"checked=\"checked\"";}}?>>
      <?php echo"$yearCurrent"; ?>
    </label>
	</div>
	<div class="radio">
    <label for="type_in-1">
      <input type="radio" name="year_in" id="year_in-2" value="<?php echo"$yearNext"; ?>"
	  <?php if(isset($_POST["year_in"])){if($_POST["year_in"]==$yearNext){echo"checked=\"checked\"";}}?>>
      <?php echo"$yearNext"; ?>
    </label>
	<span class="help-block">Winter semesters start in January, so use that new year</span>
	</div>
  </div>
</div>

<?php 
$teachersResult = $mysqli->query("SELECT * FROM teachers");
echo "<h4 style=\"text-align:center;color:red\">$errMsgTeacher</h4>";
?>
<div class="form-group">
  <label class="col-md-4 control-label" for="teacher_in">Select Teacher</label>
  <div class="col-md-4">
		<select  class="js-example-basic-single form-control" name="teacher_in">
		  <option value = "NULLTEACHER"></option>
		  <?php
		  while($rowTeach = $teachersResult->fetch_assoc()){
			echo "<option value='" . $rowTeach['netid'] . "'>" . $rowTeach['netid'] . " - " . $rowTeach['last_name'] . ", " . $rowTeach['first_name'] . "</option>";
		  }
		  ?>
		</select>
		<span class="help-block">netid - [Last Name, First Name]<br>Type a name or netid in the search bar for easier finding</span>
  </div>
</div>
<script>
$(document).ready(function() {
    $('.js-example-basic-single').select2();
});
</script>

<?php 
$studentsResult = $mysqli->query("SELECT * FROM students");
?>
<div class="form-group">
  <label class="col-md-4 control-label" for="roster_in[]">Course Roster<br>(can be done later)</label>
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