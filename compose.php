<?php
include_once "inc/user.php";
include_once "inc/misc.inc.php";
include_once "inc/alerts.inc.php";
include_once "inc/mail.inc.php";
if(!isLoggedIn()){
	header("Location: login.php?error=Please+login+first");
}

// Change password
$to = "";
$message = "";
if(isset($_POST['sendmessage']) && valid(clean($_POST['sendmessage']))){
	$to = clean($_POST['to']);
	$message = clean($_POST['message']);
	if(valid($to) && valid($message)){
		$sent = sendSimpleMail($to, $_SESSION['username'], $message);
		if($sent){
			$alerts->setSuccess("Message sent to {$to}!");
			$to = "";
			$message = "";
		}else{
		$alerts->setError("Message not sent. Unknown error.");
		}
	}else{
		$alerts->setError("Please provide both a person to send to and a message!");
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
					<span class='title'>Send mail to anyone</span>
					<p class='content'>
						Want to send a quick note to your friend? Here's the place to do it.<br>
						<form action='compose.php' method='post'>
							<table border=0>
								<tr><td>To: </td><td><input type='text' name='to' value='<?php echo $to; ?>' class='composeto'></td></tr>
								<tr><td colspan=2>Message: </td></tr>
								<tr><td colspan=2><textarea class='composemessage' name='message'><?php echo $message; ?></textarea></td></tr>
								<tr><td colspan=2><input type='submit' name='sendmessage' value='Send Message'></td></tr>
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