<?php
include_once "inc/user.inc.php";
include_once "inc/misc.inc.php";
include_once "inc/alerts.inc.php";
include_once "inc/mail.inc.php";

if(isLoggedIn()){
	header("Location: mail.php?folder=inbox&success=You+are+already+logged+in");
}

// Login function
if(isset($_POST['login']) && valid(clean($_POST['login']))){
	$username = clean($_POST['username']);
	$password = clean($_POST['password']);
	if(valid($username) && valid($password)){
		if(login($username, $password)){
			header("Location: mail.php?folder=inbox&success=You+have+been+logged+in");
		}else{
			$alerts->setError("Incorrect username or password :(");
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
					<span class='title'>Login to xMail</span>
					<p class='content'>
						Don't have an account yet? Create one <a href='register.php'>here</a> or in-game with <code>/xmail register</code><br>
						<br>
						<form action='login.php' method='post' name='login-xmail'>
							<table border=0>
								<tr><td><b>Minecraft Username</b></td><td><input type='text' name='username'></td></tr>
								<tr><td><b>xMail Password</b></td><td><input type='password' name='password'></td></tr>
								<tr><td colspan=2><input type='submit' name='login' value='Login'></td></tr>
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