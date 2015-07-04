<?php 


	$dbhost = "localhost";
	$user ="root" ;//"glib";
	$pwrd = "" ;//"glibrary";
	$dbname = "library";
try{
	$con = new PDO("mysql:host=".$dbhost.";dbname=".$dbname."; port=3306",$user, $pwrd);	
}catch(PDOException $e){var_dump($e->getMessage());exit("exception");}
//var_dump($con);
	if($con)
		$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	else
		$con=null;
	


?>
