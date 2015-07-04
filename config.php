<?php 
	$dbhost = "localhost";
	$user = "root";
	$pwrd = "";
	$dbname = "library";

	$con = mysqli_connect($dbhost, $user, $pwrd, $dbname);
	$error = mysqli_error($con);

?> 