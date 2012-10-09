<?php
if(isset($_GET['doincludes'])){
	include "connection.php";
	include "session.php";
	include "mail.php";
	include "user.php";
}
if(!isLoggedIn()){
	die("Please login first");
}
if(isset($_GET['unreadcount'])){
	$query = mysql_query("SELECT COUNT(unread) as unread FROM `mail` WHERE `to`='".$_SESSION['username']."' AND `unread`='1'")or die(mysql_error());
	$amount = mysql_result($query, 0, "unread");
	if($amount > 0){
		if(isset($_GET['raw'])){
			echo $amount;
			die();
		}
		echo "(".$amount.")";
	}else{
		echo "";
	}
	die();
}
$query = mysql_query("SELECT * FROM `mail` WHERE `to`='".$_SESSION['username']."' AND `unread`='1'") or die(mysql_error());
if(mysql_num_rows($query) > 0){
	echo "<div style='width:45%;float:left'>";
	while($a = mysql_fetch_array($query)){
		$from = clean($a['from']);
		$subject = dirty($a['message']);
		$id = $a['id'];
		$length = strlen($subject);
		if($length > 25){
			$subject = substr($subject, 0, 25)."...";
		}
		$style = "border-bottom:1px solid black;width:100%;color:black;background:url(\"images/message.png\") repeat;padding:5px;margin-bottom:10px;font-size:18px;overflow:hidden;";
		echo "<div style='$style' onClick='view($id)' id='mess$id'>$from: <i>$subject</i></div>";
	}	
	echo "</div><div style='width:45%;float:right;background:url(\"images/message.png\") repeat;padding:10px;word-wrap:break-word' id='message'>";
	echo "<center><p>No message selected</p></center>";
	echo "</div>";
	echo "<input type='hidden' id='last' value='#mess-1'>";
}else{
	echo "<center>No unread mail!</center>";
}
?>