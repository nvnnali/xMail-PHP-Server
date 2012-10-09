<?php
include "sessionclass.php";
session_start();

function getSessionList(){
	$username = $_SESSION['username'];
	if(valid($username)){
		$array = array();
		$query = mysql_query("SELECT * FROM `keys` WHERE `other`='{$_SESSION['username']}'");
		if(mysql_num_rows($query)>0){
			$i = 0;
			while($a = mysql_fetch_array($query)){
				$hash = sha1(time().$i);
				$expires = strtotime("+15 minutes");
				$array[$i] = new Session($a['ip'], $a['ip'], $hash);
				$i++;
				mysql_query("INSERT INTO `session` (`key`, `ip`, `pkey`, `sname`, `sid`, `expires`) VALUES ('{$hash}', '{$a['ip']}', '{$a['key']}', '{$a['ip']}', '{$a['id']}', '{$expires}')") or die(mysql_error());
			}
		}
		return $array;
	}else{
		return array();
	}
}

function clean($input){
	if(get_magic_quotes_gpc()){
		$input = stripslashes($input);
	}
	//$input = mysql_real_escape_string($input);
	$input = htmlentities($input, ENT_COMPAT, "UTF-8");
	return $input;
}

function valid($input){
	$input = trim($input);
	return isset($input) && $input!=null && $input!="";
}

function dirty($input){
	//if(get_magic_quotes_gpc()){
	//	$input = addslashes($input);
	//}
	//$input = mysql_real_escape_string($input);
	$input = html_entity_decode($input, ENT_COMPAT, "UTF-8");
	return $input;
}
?>