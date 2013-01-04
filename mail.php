<?php
require_once "inc/user.inc.php";
require_once "inc/misc.inc.php";
require_once "inc/alerts.inc.php";
require_once "inc/mail.inc.php";

if(!isLoggedIn()){
	header("Location: login.php?error=Please+login+first");
}

if(isset($_POST['markread']) && valid(clean($_POST['markread']))){
	$mid = clean($_POST['id']);
	if(!valid($mid)){
		$alerts->setError("Unknown message");
	}else{
		mysql_query("UPDATE `mail` SET `unread`='0' WHERE `id`='{$mid}' AND `to`='{$_SESSION['username']}' LIMIT 1") or die(mysql_error());
		$alerts->setSuccess("Message marked as read!");
	}
}
if(isset($_POST['markunread']) && valid(clean($_POST['markunread']))){
	$mid = clean($_POST['id']);
	if(!valid($mid)){
		$alerts->setError("Unknown message");
	}else{
		mysql_query("UPDATE `mail` SET `unread`='1' WHERE `id`='{$mid}' AND `to`='{$_SESSION['username']}' LIMIT 1") or die(mysql_error());
		$alerts->setSuccess("Message marked as read!");
	}
}

$folder = strtolower(clean($_GET['folder']));
if(!isset($folder) || empty($folder)){
	$folder = "inbox";
}

$page = clean($_GET['page']);
if(!isset($page) || empty($page) || !is_numeric($page)){
	$page = 1;
}
if($page < 1){
	$page = 1;
}

$perPage = 5;
$start = ($page-1)*$perPage;

$folders = array("inbox", "sent", "read");
if(!in_array($folder, $folders)){
	$alerts->setError("Unknown folder '{$folder}'! Using inbox...");
	$folder = "inbox";
}

$properFolderName = ucfirst($folder);

$mail = "";
$query = null;
$rawQuery = "";
$allMail = 0;
if($folder == "inbox"){
	$rawQuery = "WHERE `to`='{$_SESSION['username']}' AND `unread`='1' ORDER BY `id` ASC";
}else if($folder == "sent"){
	$rawQuery = "WHERE `from`='{$_SESSION['username']}' ORDER BY `id` ASC";
}else if($folder == "read"){
	$rawQuery = "WHERE `to`='{$_SESSION['username']}' AND `unread`='0' ORDER BY `id` ASC";
}
$query = mysql_query("SELECT * FROM `mail` ".$rawQuery." LIMIT {$start}, {$perPage}") or die(mysql_error());
$q2 = mysql_query("SELECT * FROM `mail` ".$rawQuery) or die(mysql_error());
$allMail = mysql_num_rows($q2);

$maxPages = mysql_result(mysql_query("SELECT COUNT(*) AS `num` FROM `mail` ".$rawQuery), 0, 'num');
$pageLine = getPageNavigation($maxPages, "mail.php", $perPage, 3, "folder={$folder}");

if($query!=null && mysql_num_rows($query)>0){
	while($a = mysql_fetch_array($query)){
		$to = $a['to'];
		$from = $a['from'];
		$message = $a['message'];
		$sent = $a['sent'];
		$complex = $a['complex'];
		$unread = $a['unread'];
		$id = $a['id'];
		
		if($complex == 1){
			$complex = true;
		}else{
			$complex = false;
		}
		
		if($unread == 1){
			$unread = true;
		}else{
			$unread = false;
		}
		
		$rawSent = (int) $sent;
		$sent = date('l, F j, Y', $rawSent);
		$time = date('g:i:s A T', $rawSent);
	
		$mail .= "<div class='mailmessage'>";
		$mail .= "<div class='info'><p><b>To: </b>{$to}<br><b>From: </b>{$from}<br><b>Sent: </b>{$sent}<br><b>Time: </b>{$time}<br><br>";
		if($folder == "inbox" || $folder == "read"){
			if($complex){
				$mail .= "<i>Go in-game to get read this mail, it has attachments!</i>";
			}else{
				$mail .= "<form action='mail.php' method='post'>";
				$mail .= "<input type='hidden' name='id' value='{$id}'>";
				if($unread){
					$mail .= "<input type='submit' name='markread' value='Mark as Read' class='mark'>";
				}else{
					$mail .= "<input type='submit' name='markunread' value='Mark as Unread' class='mark'>";
				}
				$mail .= "</form>";
			}
		}else{
			$mail .= "<i>Sorry! You cannot mark this mail as read or unread!</i>";
		}
		$mail .= "</p></div>";
		$mail .= "<div class='message'><p><b>Message: </b>{$message}</p></div>";
		$mail .= "<div class='clear'></div></div>";
	}
	$mail .= "<div class='mailmessage'><p><center>{$pageLine}<center></p></div>";
}else{
	$mail = "No Mail!";
}
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
						<?php echo $mail; ?>
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