<?php 
	session_start();
	if (isset($_SESSION['user']) && isset($_SESSION['admin'])) {
		header('Location: adminReset.html');
	}
	elseif (isset($_SESSION['user'])) {
		header('Location:adminLogin.php');
	}
	include ('../config.php');

	if (isset($_POST['submit'])) {
		$username = $_POST['username'];
		$password = $_POST['password'];

		//sanitization
		$username = stripslashes($username);
		$password = stripslashes($password);
		$username = trim($username);
		$password = trim($password);

		$username = mysqli_real_escape_string($con,$username);
		$password = mysqli_real_escape_string($con,$password);

		//hash passkey
		$hash_password =sha1($password);

		$query = "SELECT username, password FROM user 
		WHERE username = '$username' AND password = '$hash_password'";

		$mysql = mysqli_query($con,$query);
		$num_rows = mysqli_num_rows($mysql);

		if ($num_rows < 1) {
			$msg = 'Username or password does not exist';
		}

		else {
			$msg = 'Login Successful';
			$_SESSION['user'] = $username;
			$_SESSION['admin'] = $admin;

			if(!$_SESSION['admin']) {
				header('Refresh:3, url = adminReset.html');
			}
			else {
			header('Refresh:3, url=adminReset.html');
		}
		}
		mysqli_close($con);

	}
	
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<link rel="stylesheet" type="text/css" href="../css/admin.css">

    <title>Catalog Admin Login</title>

</head>


<body>
	
	<div class="login-01">
		<div class="one-login  hvr-float-shadow">
			<div class="one-login-head">
					<img src="../images/top-lock.png" alt=""/>
					<h1>LOGIN</h1>
					<!--span></span-->
			</div>
			<form action="" method="POST">
			<?php
				if (isset($msg))
					echo $msg;
				?>
				<li>
					<input type="text" class="text" name="username" value="Username" onfocus="this.value = '';" onblur="if (this.value == '') {this.value = 'Username';}" ><a href="#" class=" icon user"></a>
				</li>
				<li>
					<input type="password" name="password" placeholder="Password"><a href="#" class=" icon lock"></a>
				</li>
				<div class="p-container">
						<label class="checkbox" style="font-variant: small-caps; font-family: "century gothic"; "><input type="checkbox" name="checkbox" checked><i></i>Remember Me</label>
						<h6 style="font-variant: small-caps; font-family: "century gothic";"><a href="adminReset.html">Forgot Password ?</a> </h6>
							<div class="clear"> </div>
				</div>
				<div class="submit">
						<input type="submit"  name="submit" onclick="myFunction()" value="SIGN IN" >
				</div>
			</form>
			<span style="color:white; font-size: 90%; font-variant: small-caps; font-family: "century gothic"; ">&copy;2015. All Rights Reserved | Goodluck</span>
		</div>
	</div>
</body>


</html>