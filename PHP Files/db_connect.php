<?php
//connecting to database
$host = "localhost:3307";
$username = "root";
$user_pass = "usbw";
$database_in_use = "database_course_evaluations_project";

$mysqli = new mysqli($host, $username, $user_pass, $database_in_use);

if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}
//echo $mysqli->host_info . "<br>";
?>