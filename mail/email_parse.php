#!/usr/bin/php -q
<?php
/*
This file is to be used if you want to setup email piping for xMail.
*/

// Includes
require_once "includes/constants.inc.php";
require_once "includes/ConfigHandler.inc.php";
require_once "includes/misc.inc.php";
require_once "includes/keys.inc.php";

// Construct some classes before we get moving
$config = configHandler::singleton();

// You will need to change these values, most likely
mysql_connect($config->get("mysql.server"), $config->get("mysql.username"), $config->get("mysql.password")) or die(mysql_error());
mysql_select_db($config->get("mysql.database")) or die(mysql_error());

$email = file_get_contents('php://stdin');
preg_match_all("/(.*):\s(.*)\n/i", $email, $matches);

// You may have to change these settings due to domain changes.
// To see if they need changing, uncomment the following line:
// mail("YOUREMAILHERE", "xMail PHP server dump", print_r($matches, true));
// Look under the [2] index for the required indexes after sending an email through
// the script.

$sender 	= $matches[2][10]; 					// whoever@xmail.turt2live.com
$sender_ID	= explode('@', $sender);			// Raw from (assuming SomeName <SomeEmail@domain.ext>)
$sender_ID	= explode(" <", $sender_ID[0]);		// Split on < (just in case)
$sender_ID 	= clean($sender_ID[0]);				// whoever
$sender 	= clean($sender);
$subject 	= clean($matches[2][8]);          	// The subject
$message 	= clean($matches[2][14]); 			// If prefixed with "message: <text>"
$from 		= $matches[2][9]; 					// SomeName <SomeEmail@domain.ext>
$id 		= clean($matches[2][13]);			// If prefixed with "apikey: <key>"
$now 		= time();

// Check sent information
if(empty($subject) === true || empty($message) === true || empty($id) === true){
	notEnoughArguments($from);
}else{
	$sendAs = checkID($id);
	if($sendAs == null){
		userNotFound($from);
	}else{
		$mail_to = $sender_ID;
		$mail_from = $sendAs;
		$mail_message = $message;
		mysql_query("INSERT INTO `mail` (`to`, `from`, `message`, `unread`, `complex`, `sent`) VALUES ('$mail_to', '$mail_from', '$mail_message', '1', '0', '$now')") or error(mysql_error());
		$reply = "Your message to $mail_to was sent!\n\nMessage:\n$mail_message\n\nThanks for using xMail, $mail_from!";
		mail($from, "xMail: Your mail was sent", $reply, "From: noreply@system.turt2live.com");
	}
}

function checkID($id){
	mail("travpc@gmail.com", "sadasda", strlen($id)."");
	if(strlen($id)<5){
		return null;
	}
	$query = mysql_query("SELECT `username` FROM `users` WHERE `apikey` LIKE '$id%'");
	if(mysql_num_rows($query) == 1){
		return mysql_result($query, 0, "username");
	}
	return null;
}

function userNotFound($sendTo){
	$message = "You sent an invalid API key.\n\n" 
		."Please send mail as follows:\n\n"
		."TO: <minecraft username>@xmail.turt2live.com\n"
		."SUBJECT: Not Important\n"
		."MESSAGE:\n"
		."\tapikey: <your api key from xmail.turt2live.com>\n"
		."\tmessage: <your message to send>\n\n"
		."Thank You.";
	mail($sendTo, "xMail: Mail send error", $message, "From: noreply@system.turt2live.com");
}

function notEnoughArguments($sendTo){
	$message = "Please send mail as follows:\n\n"
		."TO: <minecraft username>@xmail.turt2live.com\n"
		."SUBJECT: Not Important\n"
		."MESSAGE:\n"
		."\tapikey: <your api key from xmail.turt2live.com>\n"
		."\tmessage: <your message to send>\n\n"
		."Thank You.";
	mail($sendTo, "xMail: Mail send error", $message, "From: noreply@system.turt2live.com");
}
?>