<?php
include "../inc/constants.php";
include "../inc/session.php";
include "../inc/mobile-detect.php";
include "../inc/connection.php";
include "../inc/mail.php";
include "../inc/user.php";

$key = $_GET['key'];

$query = mysql_query("SELECT * FROM `session` WHERE `key`='$key'");
if(valid($key) && mysql_num_rows($query)>0){
	$a = mysql_fetch_array($query);
	$expire = $a['expires'];
	if($expire > time()){
		$id = $a['sid'];
		$key = $a['pkey'];
		$ip = $a['ip'];
		if(valid($id) || valid($key)){
			mysql_query("DELETE FROM `keys` WHERE `id`='$id' AND `other`='{$_SESSION['username']}' AND `key`='$key' LIMIT 1") or die(mysql_error());
			mysql_query("DELETE FROM `serversessions` WHERE `ip`='$ip' AND `username`='{$_SESSION['username']}'") or die(mysql_error());
		}
	}
}
header("Location: ".URL."account.php?lg=1");
?>