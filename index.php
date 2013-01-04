<?php
include_once "inc/user.php";
include_once "inc/misc.inc.php";
include_once "inc/alerts.inc.php";
include_once "inc/mail.inc.php";
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
					<span class='title'>Welcome to xMail!</span>
					<p class='content'>
						xMail is a mail system for Minecraft servers that want cross-server mail. This specific server appears to be running their own local mail system, therefore mail here cannot be accessed at <a href='http://xmail.turt2live.com' target='_TOP'>xmail.turt2live.com</a>. <br>
						<br>
						Remember to have fun with xMail! It's a mail system for you to enjoy.
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