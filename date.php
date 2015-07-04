<?php 
	$dbhost = "localhost";
	$user = "root";
	$pwrd = "";
	$dbname = "library";

	$con = mysqli_connect($dbhost, $user, $pwrd, $dbname);
	$error = mysqli_error($con);

	/*$result = mysqli_query($con, "SELECT `bk_ID` FROM `books`");
	$row = mysqli_num_rows($result);

	echo $row. '<br/>';
	for($i=1; $i <= $row; $i++)
	{
		$date = rand(2003,2014);
		$query = "UPDATE books SET Date = $date WHERE bk_ID = $i";
		echo $query;
		 mysqli_query($con, $query);
		echo $i.".".$date.'<br/>';
	}*/



	/*$array = array("really long string here, boy", "this", "middling length", "larger");
	usort($array, function($a, $b) {
		return strlen($a) - strlen($b);
		var_dump($a);
		var_dump($b);
	});
	print_r($array);*/

	
		
		$query = "UPDATE books SET edition = 'Revised' WHERE category = 'Novel'";
		echo $query;
		 mysqli_query($con, $query);
		//echo $i.".".$date.'<br/>';
	
	
?>