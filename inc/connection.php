<?php
mysql_connect("localhost", "turt2l5_xmail", "xmailbukkit12") or die(mysql_error());
mysql_select_db("turt2l5_xmail") or die(mysql_error());

$ip = $_SERVER['REMOTE_ADDR'];

if(mysql_num_rows(mysql_query("SELECT id FROM `banned` WHERE `ip`='$ip'"))>0){
	die(json_encode(array("message" => "IP Ban", "status" => "ERROR", "ip" => $ip)));
}
?>