<?php
include_once "inc/user.inc.php";
include_once "inc/misc.inc.php";
include_once "inc/alerts.inc.php";
include_once "inc/mail.inc.php";

if(isLoggedIn()){
	header("Location: mail.php?folder=inbox&success=You+are+already+logged+in");
}

// Register function
if(isset($_POST['register']) && valid(clean($_POST['register']))){
	$username = clean($_POST['username']);
	$password = clean($_POST['password']);
	$cpassword = clean($_POST['cpassword']);
	if(valid($username) && valid($password) && valid($cpassword)){
		$ret = register($username, $password, $cpassword);
		if($ret == 1){
			header("Location: mail.php?folder=inbox&success=You+have+been+logged+in+and+registered");
		}else{
			if($ret == 2){
				$alerts->setError("That username is already in use :(");
			}else if($ret == 3){
				$alerts->setError("Your passwords do not seem to match :(");
			}else{
				$alerts->setError("An unknown error occured.");
			}
		}
	}else{
		$alerts->setError("Please provide a username and password!");
	}
}
?>
<html>
<head>
	<title>xMail</title>
	<link rel='stylesheet' href='style.css'>
</head>
	</body>
		<div class='wrapper'>
			<div class='header'>
				<h1>xMail</h1>
				<h2>Cross Server Minecraft Mail</h2>
				<ul class='nav'>
					<li><a href='index.php'>Home</a></li>
					<?php
					if(isLoggedIn()){
						?>
						<li><a href='compose.php'>Compose</a></li>
						<li><a href='mail.php?folder=inbox'>Inbox</a></li>
						<li><a href='account.php'>My Account</a></li>
						<li><a href='logout.php'>Logout</a></li>
						<?php
					}else{
						?>
						<li><a href='login.php'>Login</a></li>
						<li><a href='register.php'>Register</a></li>
						<?php
					}
					?>
					<li><a href='about.php'>About xMail</a></li>
				</ul>
			</div>
			<div class='body'>
				<?php
				$alerts->displayAllAlerts(); // Display alerts
				?>
				<div class='post'>
					<span class='title'>Register for xMail</span>
					<p class='content'>
						Already have an account? Login <a href='login.php'>here</a><br>
						<br>
						<form action='register.php' method='post' name='register-xmail'>
							<table border=0>
								<tr><td><b>Minecraft Username</b></td><td><input type='text' name='username'></td></tr>
								<tr><td><b>Desired Password</b></td><td><input type='password' name='password'></td></tr>
								<tr><td><b>Confirm Password</b></td><td><input type='password' name='cpassword'></td></tr>
								<tr><td colspan=2><input type='submit' name='register' value='Register'></td></tr>
							</table>
						</form>
					</p>
				</div>
			</div>
			<div class='footer'>
				Original design by <a href='http://xmail.turt2live.com' target='_TOP'>turt2live</a>. xMail is designed and tested for Bukkit servers only.<br>
				This template is meant to be replaced. Head over to <a href='http://webchat.esper.net/?nick=xMailUser...&channels=turt2live' target='_TOP'>#turt2live on EsperNet</a> for help.
			</div>
		</div>
	</body>
</html>