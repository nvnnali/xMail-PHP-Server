<?php
function onRegister($user){
	
}

function onSend($to, $from, $message){
	$query = mysql_query("SELECT `email` FROM `users` WHERE `username`='$to'");
	if(mysql_num_rows($query)>0){
		$email = mysql_result($query, 0, "email");
		mail($email, "New mail from $from!", "You have new mail from $from, you can reply to this email to send a message back! Here's the message:\n\n".dirty($message), "From: $from@xmail.turt2live.com\r\nReply-To: $from@xmail.turt2live.com\r\nReturn-Path: $from@xmail.turt2live.com");
	}
}

function onComplexSend($to, $from, $message){
	$query = mysql_query("SELECT `email` FROM `users` WHERE `username`='$to'");
	if(mysql_num_rows($query)>0){
		$email = mysql_result($query, 0, "email");
		mail($email, "New mail from $from! (in-game attachments)", "You have new mail (with in-game attachments) from $from, you can reply to this email to send a message back, but don't forget to login to an xMail server to get your attachments! Here's the message:\n\n".dirty($message), "From: $from@xmail.turt2live.com\r\nReply-To: $from@xmail.turt2live.com\r\nReturn-Path: $from@xmail.turt2live.com");
	}
}
?>