<?php
include_once "inc/user.php"; // For database connection

function sendSimpleMail($to, $from, $message){
	$now = time();
	$ip = $_SERVER['REMOTE_ADDR'];
	$query = mysql_query("INSERT INTO `mail` (`to`, `from`, `message`, `complex`, `sent`, `unread`, `sent_from`, `pluginname`) 
							VALUES ('{$to}', '{$from}', '{$message}', '0', '{$now}', '1', '{$ip}', 'xMail')") or die(mysql_error());
	if($query){
		return true;
	}else{
		return false;
	}
}

function sendComplexMail($to, $from, $message, $attachments = array()){
	if(count($attachments)==0){
		return sendSimpleMail($to, $from, $message);
	}else{
		return false;
	}
}
?>