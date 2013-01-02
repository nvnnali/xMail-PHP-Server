<?php
include "inc/user.php";
include "inc/misc.inc.php";
include "inc/alerts.inc.php";
if(!isLoggedIn()){
	header("Location: login.php?error=Please+login+first");
}

// Change password
if(isset($_POST['changepassword']) && valid(clean($_POST['changepassword']))){
	$c = sha1(clean($_POST['cpwd']));
	$n = sha1(clean($_POST['npwd']));
	$n2 = sha1(clean($_POST['npwd2']));
	if(testLogin($_SESSION['username'], $c, false)){
		if($n == $n2){
			changePassword($_SESSION['username'], $n, false);
		}else{
			$alerts->setError("New passwords don't match!");
		}
	}else{
		$alerts->setError("Incorrect current password!");
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
					<span class='title'>Your account, <?php echo $_SESSION['username']; ?></span>
					<p class='content'>
						You have sent <?php echo getNumMailSent($_SESSION['username']); ?> mail messages, <?php echo getPercentOfSentRead($_SESSION['username']); ?>% of which have been read.<br>
						<br>
						<b>Change your password:</b>
						<form action='account.php' method='post'>
							<table border=0>
								<tr><td>Current Password: </td><td><input type='password' name='cpwd'></td></tr>
								<tr><td>New Password: </td><td><input type='password' name='npwd'></td></tr>
								<tr><td>Repeat New Password: </td><td><input type='password' name='npwd2'></td></tr>
								<tr><td colspan=2><input type='submit' name='changepassword' value='Change Password'></td></tr>
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