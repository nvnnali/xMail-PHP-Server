<?php
include "inc/user.php";
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
					<span class='title'>About xMail</span>
					<p class='content'>
						xMail allows you, as a player, to send mail to yourself or others on another server. With xMail you can send money, items, or animals and monsters (if the sever allows it). On top of all of the simple mail functions to xMail, the project also allows custom server implementations, such as this one.
					</p>
				</div>
				<div class='post'>
					<span class='title'>Custom Server Implementations</span>
					<p class='content'>
						Creating your own server for xMail has it's advantages, such as a more localized mail system. The server provided with this basic website is designed for 'cloud' server systems although it can be adapted for 'online' mail access for a single server. <br>
						<br>
						Whether you are using xMail to link all of your servers together, or using it to provide mail access online, xMail is a perfect solution to any server looking for a better mail plugin.
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