<?php
include "inc/user.php";
include "inc/misc.inc.php";
include "inc/alerts.inc.php";
if(!isLoggedIn()){
	header("Location: login.php?error=Please+login+first");
}
$folder = strtolower(clean($_GET['folder']));
if(!isset($folder) || empty($folder)){
	$folder = "inbox";
}
$folders = array("inbox", "sent", "read");
if(!in_array($folder, $folders)){
	$alerts->setError("Unknown folder '{$folder}'! Using inbox...");
	$folder = "inbox";
}
$properFolderName = ucfirst($folder);
$alerts->setWarning("Mail not implemented");
?>
<html>
<head>
	<title>xMail</title>
	<link rel='stylesheet' href='style.css'>
</head>
	</body>
		<div class='wrapper'>
			<div class='header' style='height:160px;'>
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
				<ul class='nav' style='margin-top:15px;'>
					<li><a href='mail.php?folder=inbox'>New Mail</a></li>
					<li><a href='mail.php?folder=read'>Read Mail</a></li>
					<li><a href='mail.php?folder=sent'>Sent Mail</a></li>
				</ul>
			</div>
			<div class='body'>
				<?php
				$alerts->displayAllAlerts(); // Display alerts
				?>
				<div class='post'>
					<span class='title'><?php echo $properFolderName; ?></span>
					<p class='content'>
						No Mail!
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