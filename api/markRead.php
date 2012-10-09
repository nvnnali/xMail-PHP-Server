<?php
include "../inc/connection.php";
include "../inc/session.php";
include "../inc/mail.php";
include "../inc/user.php";

if(!isLoggedIn()){
	die("Error: not logged in");
}

$id = $_GET['id'];
if(!valid($id)){
	die("Invalid ID");
}

$read = 0;
if(valid($_GET['inverse'])){
	$read = 1;
}

$id = clean($id);
if(mysql_num_rows(mysql_query("SELECT * FROM `mail` WHERE `id`='$id' AND `to`='".$_SESSION['username']."'")) == 0){
	echo "This is not your message.";
}else{
	if(mysql_num_rows(mysql_query("SELECT * FROM `mail` WHERE `id`='$id' AND `to`='".$_SESSION['username']."' AND `complex`='1'")) > 0){
		die("Cannot mark as unread or read because this message has attachments");
	}
	mysql_query("UPDATE `mail` SET `unread`='$read' WHERE `id`='$id' LIMIT 1") or die(mysql_error());
	if($read == 0){
		echo "<span style='color:white'>Message marked as read. (<span onclick='mark_unread($id)' style='color:#0082B4;'>mark unread</span>)</span>";
	}else{
		echo "<span style='color:white'>Message marked as unread. (<span onclick='mark_read($id)' style='color:#0082B4;'>mark read</span>)</span>";
	}
}
?>