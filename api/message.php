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

$id = clean($id);
if(mysql_num_rows(mysql_query("SELECT * FROM `mail` WHERE `id`='$id' AND `to`='".$_SESSION['username']."'")) == 0){
	echo "This is not your message.";
}else{
	$a = mysql_fetch_array(mysql_query("SELECT * FROM `mail` WHERE `id`='$id' AND `to`='".$_SESSION['username']."'"));
	echo "<b>From: </b>".clean($a['from']);
	echo "<br><b>Message:</b><br>".dirty($a['message']);
	if($a['complex'] == 0){
		if($a['unread']==1){
			echo "<hr><center><span onClick='markRead($id)' id='status' style='color:#0082B4;'>Mark as Read</span></center>";
		}else{
			echo "<hr><center><span style='color:white' id='status'>Message marked as read. (<span onclick='markUnread($id)' style='color:#0082B4;'>mark unread</span>)</span></center>";
		}
	}else{
		echo "<hr><center>cannot mark as read because this message has attachments</center>";
	}
	//echo "<br><center><span id='reply' style='padding:none;margin:none;font-size:18px;padding-bottom:5px;cursor:pointer;' class='recent' onclick='$(\"#dialog-form\").dialog(\"open\");$(\"#r-to\").val(\"".clean($a['from'])."\");$(\"#r-message\").val(\"\");'>Reply</span></center>";
}
?>