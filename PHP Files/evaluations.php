<!DOCTYPE HTML>
<html>
<head>
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

<!-- jQuery library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

<!-- Latest compiled JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

</head>

<body style="background-image:url(http://eskipaper.com/images/background-pictures-6.jpg);background-repeat:no-repeat;background-size:cover;overflow-x:hidden;">
<!--<body style="background-color:#E6E6FA">-->
<!-- <h1>Course Evaluation <br><br> </hl> -->

<?php
session_start();
include "session_check.php";
if($_SESSION["type"] != "student"){
	header('Refresh: 1; URL = logout.php');
	die("<h2 style=\"color:white;font-weight:bold;text-align:center;\">You are not a student!</h2><h3 style=\"color:white;font-weight:bold;text-align:center;\">Logging out now</h3>");
}
include "db_connect.php";

date_default_timezone_set("America/New_York");
$current_time12 = date("h:i:sa");
$current_dateMDY = date("m-d-Y");
$current_time = date("H:i:s");
$current_date = date("Y-m-d");
$current_wkday = date("l", strtotime($current_date));

$username = $_SESSION['username'];
$stmt = $mysqli->prepare("SELECT * FROM course_rosters a JOIN courses b 
		 WHERE (a.Course_ID=b.Course_ID) AND (a.semester=b.semester) AND (a.student_netid = ?) AND 
		 (b.start_date <= '$current_date') AND (b.end_date >= '$current_date')");
$stmt->bind_param("s",$username);
$stmt->execute();
$classResult = $stmt->get_result();
$stmt->close();
if($classResult->num_rows == 0){	//can add a check later to see if enrolled in anything for the semester
	$mysqli->close();
	header('Refresh: 2; URL = logout.php');
	die("<h2 style=\"color:white;font-weight:bold;text-align:center;\">You are not enrolled in any courses in the system at this time</h2><h3 style=\"color:white;font-weight:bold;text-align:center;\">Logging out now</h3>");
}

$errMsgGrade = $errMsgClass = $comment_in = $errMsgEval = "";
$grade_in = 10;
$submitted = false;

if(isset($_POST['grade_in'])){
	$grade_in = $_POST['grade_in'];
}
if($_SERVER["REQUEST_METHOD"] == "POST"){
	if($grade_in == 10){
		$errMsgGrade = "MUST SELECT A GRADE!";
	}
	if(empty($_POST["class_in"])){
		$errMsgClass = "MUST SELECT A CLASS!";
	}
	if( (!empty($_POST["grade_in"])) & (!empty($_POST["class_in"])) ){
		$class_In = $_POST["class_in"];
		list($class_Input, $semester_Input) = explode("@@@@", $class_In);
		$grade_Input = $_POST["grade_in"];
		$comment_Input = addslashes($_POST["comment_in"]);
		$last_added_check = $mysqli->query("SELECT * FROM course_rosters
		WHERE (Course_ID = '$class_Input') AND (student_netid = '$username') AND (semester = '$semester_Input') AND (last_added='$current_date')");
		if($last_added_check->num_rows == 0){
			try{
				$mysqli->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
				if( !($stmt = $mysqli->prepare("INSERT INTO course_eval (Grade, Comment, Course_ID, semester) 
				VALUES('$grade_Input', ?, '$class_Input', '$semester_Input')")) )
					throw new Exception();
				$stmt->bind_param("s",$comment_Input);
				if( !($stmt->execute()) )
					throw new Exception();
				$stmt->close();
				$a = $mysqli->query("UPDATE course_rosters SET last_added='$current_date' 
				WHERE (Course_ID = '$class_Input') AND (student_netid = '$username') AND (semester = '$semester_Input')");
				if(!$a)
					throw new Exception();
				$mysqli->commit();
				$submitted = true;
			} catch(Exception $e){
				$mysqli->rollback();
				header('Refresh: 2; URL = evaluations.php');
				die("<h2 style=\"color:white;font-weight:bold;text-align:center;\">UNEXPECTED ERROR WITH EVAL</h2><h3 style=\"color:white;font-weight:bold;text-align:center;\">Going back to evaluation page</h3>");
			}
			$mysqli->close();
		}
		else{
			$errMsgEval = "You have already submitted a course evaluation for " . $class_Input ." ". $semester_Input . " today!";
		}
	}
}
?>

<?php

if($submitted){
	echo "<h2 style=\"color:white;font-weight:bold;text-align:center;\">Evaluation Submitted</h2><h3 style=\"color:white;font-weight:bold;text-align:center;\">Logging out now</h3>";
	header('Refresh: 1; URL = logout.php');
}
else{
	echo "<h2 style=\"text-align:center;color:red\">$errMsgEval</h2>";
?>
<form class="form-horizontal" method="post" action = "<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
<fieldset>

<!-- Form Name -->
<h1 style="color:white;font-weight:bold;text-align:center;">Course Evaluation Submission</h1>
<hr>
<?php echo "<h4 style=\"text-align:center;color:red\">$errMsgGrade</h4>";?>
<!-- Multiple Radios -->
<div class="form-group">
  <label style="color:white;font-weight:bold;" class="col-md-4 control-label" for="grade_in">Grade</label>
  <div class="col-md-4">
  <div class="radio">
    <label style="color:white;" for="grade_in-0">
      <input type="radio" name="grade_in" id="grade_in-0" value=0 <?php if(isset($_POST['grade_in']) && $_POST["grade_in"] == 0){echo 'checked="checked"';} ?>>
      0 (very bad)
    </label>
	</div>
  <div class="radio">
    <label style="color:white;" for="grade_in-1">
      <input type="radio" name="grade_in" id="grade_in-1" value=1 <?php if(isset($_POST['grade_in']) && $_POST["grade_in"] == 1){echo 'checked="checked"';} ?>>
      1 (not good)
    </label>
	</div>
  <div class="radio">
    <label style="color:white;" for="grade_in-2">
      <input type="radio" name="grade_in" id="grade_in-2" value=2 <?php if(isset($_POST['grade_in']) && $_POST["grade_in"] == 2){echo 'checked="checked"';} ?>>
      2 (ok)
    </label>
	</div>
  <div class="radio">
    <label style="color:white;" for="grade_in-3">
      <input type="radio" name="grade_in" id="grade_in-3" value=3 <?php if(isset($_POST['grade_in']) && $_POST["grade_in"] == 3){echo 'checked="checked"';} ?>>
      3 (very good)
    </label>
	</div>
  <div class="radio">
    <label style="color:white;" for="grade_in-4">
      <input type="radio" name="grade_in" id="grade_in-4" value=4 <?php if(isset($_POST['grade_in']) && $_POST["grade_in"] == 4){echo 'checked="checked"';} ?>>
      4 (excellent)
    </label>
	</div>
  </div>
</div>


<!-- Textarea -->
<div class="form-group">
  <label style="color:white;font-weight:bold;" class="col-md-4 control-label" for="comment_in">Comments</label>
  <div class="col-md-4">                     
    <textarea class="form-control" id="comment_in" name="comment_in"><?php if(isset($_POST['comment_in'])){echo $_POST['comment_in']; }?></textarea>
	<span style="color:oldlace;" class="help-block">Additional comments, suggestions, or questions</span> 
  </div>
</div>

<?php echo "<h4 style=\"text-align:center;color:red\">$errMsgClass</h4>";?>

<!-- Select Basic -->
<div class="form-group">
  <label style="color:white;font-weight:bold;" class="col-md-4 control-label" for="class_in">Select Class</label>
  <div class="col-md-4">
    <select id="class_in" name="class_in" class="form-control">
      	<?php
		// $username = $_SESSION['username'];
		// $sql = "SELECT a.Course_ID, b.name FROM course_rosters a JOIN courses b WHERE a.student_netid = '$username'";
		// $result = $mysqli->query($sql);
		while ($row = $classResult->fetch_assoc()) {
			$row_in = $row['Course_ID'] . "@@@@" . $row['semester'];
			//list($one, $two) = $preg_split("@@@@", $row_in);
			if( ($row['start_time'] <= $current_time) & ($row['end_time'] >= $current_time) & 
				($row['start_date'] <= $current_date) & ($row['end_date'] >= $current_date) & 
				((($row['mon']==1)&($current_wkday == "Monday")) || (($row['tues']==1)&($current_wkday == "Tuesday"))|| 
				(($row['wed']==1)&($current_wkday == "Wednesday")) || (($row['thurs']==1)&($current_wkday == "Thursday")) || 
				(($row['fri']==1)&($current_wkday == "Friday")) || (($row['sat']==1)&($current_wkday == "Saturday"))|| 
				(($row['sun']==1)&($current_wkday == "Sunday"))) ){
					echo "<option selected='".selected."' value='" . $row_in . "'>" . $row['semester'] . " " . htmlspecialchars($row['Course_ID'], ENT_QUOTES, 'UTF-8') . " - " . htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8') . "</option>";
			}
			else{
				echo "<option value='". $row_in ."'>" . $row['semester'] . " " . htmlspecialchars($row['Course_ID'], ENT_QUOTES, 'UTF-8') . " - " . htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8') . "</option>";
			}
		}
		?>
    </select>
	<span style="color:oldlace;" class="help-block">Class you are currently in at this time will be chosen by default<br>Change the selected class if needed</span>  
  </div>
</div>

<!-- Button -->
<div class="form-group">
  <label class="col-md-4 control-label" for="submit"></label>
  <div class="col-md-4">
    <button id="submit" name="submit" class="btn btn-primary">Submit</button>
  </div>
</div>

</fieldset>
</form>

<?php 
echo "<h4 style=\"text-align:center;color:white;font-weight:bold;\">$current_time12 &nbsp $current_dateMDY &nbsp $current_wkday</h4>";
?>

<form class="form-horizontal" action="logout.php">
<fieldset>

<!-- Button -->
<div class="form-group">
  <label class="col-md-2 control-label" for="logout"></label>
  <div class="col-md-2">
    <button id="logout" name="logout" class="btn btn-danger">Logout</button>
  </div>
</div>

</fieldset>
</form>
<?php
//echo "<h4 style=\"text-align:center;color:white;font-weight:bold;\">$current_time12 &nbsp $current_dateMDY &nbsp $current_wkday</h4>";
}
// if($current_wkday == "Thursday")
	// echo "yes";
//include "display_all_evals.php";
?>

</body>

</html>