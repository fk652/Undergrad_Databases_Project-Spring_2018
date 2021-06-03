<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
<style>

</style>
</head>

<body style="background-image:url(https://i.ytimg.com/vi/Z6eG4pemWs4/maxresdefault.jpg);overflow-x:auto;">
<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>
<?php
session_start();
include "session_check.php";
if($_SESSION["type"] != "teacher"){
	header('Refresh: 1; URL = logout.php');
	die("<h2 style=\"color:white;font-weight:bold;text-align:center;\">You are not a teacher!</h2><h3 style=\"color:white;font-weight:bold;text-align:center;\">Logging out now</h3>");
}
include "db_connect.php";

$username = $_SESSION['username'];
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
	$sql1 = "SELECT b.Course_ID, b.name, b.semester 
	FROM teachers a JOIN courses b 
	WHERE (a.netid = b.teacher_netid) AND (a.netid = '$username')
	ORDER BY b.start_date DESC";
	$courseResult = $mysqli->query($sql1);
	if($courseResult->num_rows == 0){
		header('Refresh: 1; URL = logout.php');
		die("<h2 style=\"color:white;font-weight:bold;text-align:center;\">No courses found for you</h2><h3 style=\"color:white;font-weight:bold;text-align:center;\">Logging out now</h3>");
	}
	else{
		echo "<h1 style=\"color:white;font-weight:bold;text-align:center;\">All Course Reports</h1>";
		echo "<hr>";
		$number = 0;
		$dataPoints = array();
		$dataLabels = array();
		while($row1 = $courseResult->fetch_assoc()){
			$number += 1;
			$title = $row1['Course_ID'] . " - " . $row1['name'] . " (" . $row1['semester'] . ")";
			$course = $row1['Course_ID'];
			$semester = $row1['semester'];
?>
<!-- <div id="accordion"> use this to make one open at a time-->
 <!-- outer section-->
  <div class="card" style="width:75%;margin:auto">
    <div class="card-header" id="heading<?php echo"$number"; ?>" data-toggle="collapse" data-target="#collapse<?php echo"$number"; ?>">
      <h5 class="mb-0">
        <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapse<?php echo"$number"; ?>" aria-expanded="false" aria-controls="collapse<?php echo"$number"; ?>">
          <?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?>
        </button>
      </h5>
    </div>
    <div id="collapse<?php echo"$number"; ?>" class="collapse" aria-labelledby="heading<?php echo"$number"; ?>" data-parent="#accordion">
      <div class="card-body">
	  
	  
				 <!-- new inner section-->
				<div>
				  <div class="card" style="margin:auto">
					<div class="card-header" data-toggle="collapse" data-target="#collapseOne<?php echo"$number"; ?>" id="headingOne<?php echo"$number"; ?>">
					  <h5 class="mb-0">
						<button class="btn btn-link" data-toggle="collapse" data-target="#collapseOne<?php echo"$number"; ?>" aria-expanded="false" aria-controls="collapseOne<?php echo"$number"; ?>">
						  Average Grades Chart
						</button>
					  </h5>
					</div>
					<div id="collapseOne<?php echo"$number"; ?>" class="collapse" aria-labelledby="headingOne<?php echo"$number"; ?>" data-parent="#accordion">
					  <div class="card-body">
					
								<!-- the stuff inside-->
							<?php echo"<h6 style=\"text-align:center;\">Session Dates are sorted by most recent first.</h6><h6 style=\"text-align:center;\">Only one column filter will work at a time.</h6><br>";
							$sql2 = "SELECT DATE_FORMAT(date(c.Date_added),\"%m-%d-%Y\") as 'Session Date', AVG(c.Grade) as 'Average Grade'
									FROM teachers a JOIN courses b JOIN course_eval c
									WHERE (a.netid = b.teacher_netid) AND (b.Course_ID = c.Course_ID)
										AND (a.netid = '$username') AND (b.Course_ID = '$course') AND (b.semester = '$semester')
									GROUP BY date(c.Date_added)
									ORDER BY c.Date_added DESC";
							$averageResult = $mysqli->query($sql2);
							$sumAverage = 0;
							$sumNum = $averageResult->num_rows;
							while($row2 = $averageResult->fetch_assoc()){
								$sumAverage += $row2['Average Grade'];
							}
							$totalAverage = round(($sumAverage/$sumNum),4);
							echo"<h4 style=\"text-align:center;font-weight:bold;\">CUMULATIVE SEMESTER AVERAGE: $totalAverage</h4>";
							?>					
							<div class="container" style="text-align:center;">
								<div class="row">
									<div class="panel panel-primary filterable">
										<div class="panel-heading">
											<div class="pull-right">
												<button class="btn btn-default btn-xs btn-filter"><span class="glyphicon glyphicon-filter"></span>Filter Reset</button>
												<span>reset filter settings and go back to the top of the table</span>
											</div>
										</div>
										
										<div style="max-height:1000px;overflow-y:auto">
										<table class="table table-bordered table-striped" style="width: 50%;margin:auto;">
											<thead class="thead-light">
												<tr class="filters">
													<th><input type="text" class="form-control" placeholder="Date" enabled></th>
													<th><input type="text" class="form-control" placeholder="Average" enabled></th>
												</tr>
												</thead>
												<thead class="thead-dark">
												<tr>
													<th style="width: 20%;text-align:center;">Session Date</th>
													<th style="width: 20%;text-align:center;">Average Grade</th>
												</tr>
											</thead>
											<tbody>
												<?php
												$averageResult = $mysqli->query($sql2);
												while($row2 = $averageResult->fetch_assoc()){
													$sessionDate = $row2['Session Date'];
													$averageGrade = $row2['Average Grade'];
												?>
												<tr>
													<td style="text-align:center;"><?php echo"$sessionDate"; ?></td>
													<td style="text-align:center;"><?php echo"$averageGrade"; ?></td>
												</tr>
												<?php } ?>
											</tbody>
										</table>
										</div>
										
										<div class="panel-heading">
										<div class="pull-right">
											<button class="btn btn-default btn-xs btn-filter"><span class="glyphicon glyphicon-filter"></span> Filter Reset</button>
											<span>reset filter settings and go back to the top of the table</span>
										</div>
										</div>
										
									</div>
								</div>
							</div>
						
					 </div>
					</div>
				  </div>
				  
				  <br>
			   <!-- new inner section-->
				<div>
				  <div class="card" style="margin:auto">
					<div class="card-header" data-toggle="collapse" data-target="#collapseOne2<?php echo"$number"; ?>" id="headingOne2<?php echo"$number"; ?>">
					  <h5 class="mb-0">
						<button class="btn btn-link" data-toggle="collapse" data-target="#collapseOne2<?php echo"$number"; ?>" aria-expanded="false" aria-controls="collapseOne2<?php echo"$number"; ?>">
						  Average Grades Graph
						</button>
					  </h5>
					</div>
					<div id="collapseOne2<?php echo"$number"; ?>" class="collapse" aria-labelledby="headingOne2<?php echo"$number"; ?>" data-parent="#accordion">
					  <div class="card-body">
					  
					  <!-- the stuff inside-->
						<?php
						$sql4 = "SELECT DATE_FORMAT(date(c.Date_added),\"%m-%d-%Y\") as 'Session Date', AVG(c.Grade) as 'Average Grade'
								FROM teachers a JOIN courses b JOIN course_eval c
								WHERE (a.netid = b.teacher_netid) AND (b.Course_ID = c.Course_ID)
									AND (a.netid = '$username') AND (b.Course_ID = '$course') AND (b.semester = '$semester')
								GROUP BY DATE_FORMAT(date(c.Date_added),\"%m-%d-%Y\")
								ORDER BY c.Date_added ASC";
						$averageResult2 = $mysqli->query($sql4);
						array_push($dataPoints,array());
						$index = ($number-1);
						array_push($dataPoints, array());
						array_push($dataLabels, array());
						while($row4 = $averageResult2->fetch_assoc()){
							$sessionDate2 = $row4['Session Date'];
							$averageGrade2 = $row4['Average Grade'];
							array_push($dataPoints[$index], $averageGrade2);
							array_push($dataLabels[$index], $sessionDate2);
						}
						?>
						<canvas id="line-chart<?php echo"chart$number"; ?>" width="800" height="450"></canvas>
						<script>
						new Chart(document.getElementById("line-chart<?php echo"chart$number"; ?>"), {
						  type: 'line',
						  data: {
							labels: <?php echo json_encode($dataLabels[$index]); ?>,
							datasets: [{ 
								data: <?php echo json_encode($dataPoints[$index]); ?>,
								label: "Average Grade",
								borderColor: "#3e95cd",
								pointRadius: 5,
								pointHoverRadius: 7
							  } 
							]
						  },
						  options: {
							title: {
							  display: true,
								fontSize: 30,
							  text: '<?php echo"$course ($semester) Average Grades Graph";?>'
							},
							 legend: {
								display: false
							 },
							scales: {
								yAxes : [{
									ticks : {
										max : 4,    
										min : 0
									},
								  scaleLabel: {
									display: true,
									fontSize: 25,
									labelString: 'Average Grade'
								  }
								}],
								xAxes : [{
								  scaleLabel: {
									display: true,
									fontSize: 25,
									labelString: 'Session Date'
								  }
								}]
							},
							elements: {
								line: {
									tension: 0
								}
							}
						  }
						});
						</script>
						
					 </div>
					</div>
				  </div>
				  
				  <br>
				  <!-- new inner section-->
				  <div class="card" style="margin:auto;">
					<div class="card-header" id="headingTwo<?php echo"$number"; ?>" data-toggle="collapse" data-target="#collapseTwo<?php echo"$number"; ?>">
					  <h5 class="mb-0">
						<button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseTwo<?php echo"$number"; ?>" aria-expanded="false" aria-controls="collapseTwo<?php echo"$number"; ?>">
						  Individual Course Evaluations Chart
						</button>
					  </h5>
					</div>
					<div id="collapseTwo<?php echo"$number"; ?>" class="collapse" aria-labelledby="headingTwo<?php echo"$number"; ?>" data-parent="#accordion">
					  <div class="card-body">
					  
							  <!-- the stuff inside-->
							  <?php echo"<h6 style=\"text-align:center;\">Session Dates are sorted by most recent first and then Grades are sorted by lowest first</h6><h6 style=\"text-align:center;\">Only one column filter will work at a time.</h6><br>";?>
							<div class="container">
							<div class="row">
								<div class="panel panel-primary filterable">
									<div class="panel-heading">
										<div class="pull-right">
											<button class="btn btn-default btn-xs btn-filter"><span class="glyphicon glyphicon-filter"></span> Filter Reset</button>
											<span>reset filter settings and go back to the top of the table</span>
										</div>
									</div>
									
									<div style="max-height:1000px;overflow-y:auto">
									<table class="table table-bordered table-striped">
										<thead class="thead-light">
											<tr class="filters">
												<th><input type="text" class="form-control" placeholder="Date" enabled></th>
												<th><input type="text" class="form-control" placeholder="Grade" enabled></th>
												<th><input type="text" class="form-control" placeholder="Comment" enabled></th>
											</tr>
											</thead>
											<thead class="thead-dark">
											<tr>
												<th style="text-align:center;width:15%">Session Date</th>
												<th style="text-align:center;width:10%">Grade</th>
												<th style="text-align:center;width:90%">Comment</th>
											</tr>
										</thead>
										<tbody>
											<?php
											$sql3 = "SELECT DATE_FORMAT(date(a.Date_added),\"%m-%d-%Y\") AS 'Session Date', a.Grade, a.Comment
													FROM course_eval a
													WHERE (a.Course_ID = '$course') AND (a.semester = '$semester')
													ORDER BY a.Date_added DESC, a.Grade ASC";
											$evalResult = $mysqli->query($sql3);
											while($row3 = $evalResult->fetch_assoc()){
												$dateAdded = $row3['Session Date'];
												$grade = $row3['Grade'];
												$comment = $row3['Comment'];
											?>
											<tr>
												<td style="text-align:center;"><?php echo"$dateAdded"; ?></td>
												<td style="text-align:center;"><?php echo"$grade"; ?></td>
												<td style="text-align:left;"><?php echo htmlspecialchars($comment, ENT_QUOTES, 'UTF-8'); ?></td>
											</tr>
											<?php } ?>
										</tbody>
									</table>
									</div>
									
									<div class="panel-heading">
										<div class="pull-right">
											<button class="btn btn-default btn-xs btn-filter"><span class="glyphicon glyphicon-filter"></span> Filter Reset</button>
											<span>reset filter settings and go back to the top of the table</span>
										</div>
									</div>
								</div>
							</div>
						</div>
						  
					</div>
					</div>
				  </div>

				</div>
		</div>
    </div>
    </div>
  </div>
<br><br>
<?php
		}
	}
$mysqli->close();
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

<script>
$(document).ready(function(){
    $('.filterable .btn-filter').click(function(){
        var $panel = $(this).parents('.filterable'),
        $filters = $panel.find('.filters input'),
        $tbody = $panel.find('.table tbody');
		$filters.val('').prop('disabled', false);
		$filters.first().focus();
		$tbody.find('.no-result').remove();
		$tbody.find('tr').show();
    });

    $('.filterable .filters input').keyup(function(e){
        /* Ignore tab key */
        var code = e.keyCode || e.which;
        if (code == '9') return;
        /* Useful DOM data and selectors */
        var $input = $(this),
        inputContent = $input.val().toLowerCase(),
        $panel = $input.parents('.filterable'),
		
        column = $panel.find('.filters th').index($input.parents('th')),
        $table = $panel.find('.table'),
        $rows = $table.find('tbody tr');
		if(!$table) $table = $panel;
        /* Dirtiest filter function ever ;) */
        var $filteredRows = $rows.filter(function(){
            var value = $(this).find('td').eq(column).text().toLowerCase();
            return value.indexOf(inputContent) === -1;
        });
        /* Clean previous no-result if exist */
        $table.find('tbody .no-result').remove();
        /* Show all rows, hide filtered ones (never do that outside of a demo ! xD) */
        $rows.show();
        $filteredRows.hide();
        /* Prepend no-result row if all rows are filtered */
        if ($filteredRows.length === $rows.length) {
            $table.find('tbody').prepend($('<tr class="no-result text-center"><td colspan="'+ $table.find('.filters th').length +'">No result found</td></tr>'));
        }
		
		
    });
});
</script>
</body>

</html>