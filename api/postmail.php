<?php
include "../inc/connection.php";
include "../inc/session.php";
include "../inc/mail.php";
include "../inc/user.php";
include "../mail/includes/keys.inc.php";
$from = clean($_POST['from']);
$to = clean($_POST['to']);
$message = clean($_POST['message']);
$now = time();
if(valid($from) && valid($to) && valid($message)){
	if(userExists($to)){
		mysql_query("INSERT INTO `mail` (`to`, `from`, `message`, `complex`, `sent`, `unread`) VALUES ('$to', '$from', '$message', 0, '$now', 1)") or die(mysql_error());
		echo "<span style='color:green;'>Message sent!</span>";
	}else{
		echo "<span style='color:red;'>User does not exist</span>";
	}
}else{
	echo "<span style='color:red;'>Error: Missing information</span>";
}
?>