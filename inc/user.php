<?php
function isLoggedIn(){
	return isset($_SESSION['username']);
}

function isSearchExempt(){
	if(mysql_num_rows(mysql_query("SELECT id FROM users WHERE username='".$_SESSION['username']."' AND searchexempt='1'"))>0){
		return true;
	}
	return false;
}

function toggleSearchExempt(){
	if(isSearchExempt()){
		mysql_query("UPDATE users SET searchexempt='0' WHERE username='".$_SESSION['username']."'");
	}else{
		mysql_query("UPDATE users SET searchexempt='1' WHERE username='".$_SESSION['username']."'");
	}
}

function getUnread(){
	if(isLoggedIn()){
		$query = mysql_query("SELECT COUNT(unread) as unread FROM `mail` WHERE `to`='".$_SESSION['username']."' AND `unread`='1'")or die(mysql_error());
		return mysql_result($query, 0, "unread");
	}
	return 0;
}

function getAPIKey(){
	if(isLoggedIn()){
		$key = mysql_result(mysql_query("SELECT apikey FROM `users` WHERE username='".$_SESSION['username']."' LIMIT 1"), 0, "apikey");
		$id =  mysql_result(mysql_query("SELECT id FROM `users` WHERE username='".$_SESSION['username']."' LIMIT 1"), 0, "id");
		if(strlen($key)<6){
			$key = sha1($id.time().genAPIKey());
			mysql_query("UPDATE users SET apikey='".$key."' WHERE `id`='$id'");
		}
		return substr($key, 0, 6);
	}else{
		return "none";
	}
}

function genAPIKey(){
	$now = time();
	return sha1($now.create_guid().$now.rand());
}

function create_guid($namespace = '') {     
	$guid = '';
	$uid = uniqid("", true);
	$data = $namespace;
	$data .= $_SERVER['REQUEST_TIME'];
	$data .= $_SERVER['HTTP_USER_AGENT'];
	$data .= $_SERVER['LOCAL_ADDR'];
	$data .= $_SERVER['LOCAL_PORT'];
	$data .= $_SERVER['REMOTE_ADDR'];
	$data .= $_SERVER['REMOTE_PORT'];
	$hash = strtoupper(hash('ripemd128', $uid . $guid . md5($data)));
	$guid = '{' .   
			substr($hash,  0,  8) . 
			'-' .
			substr($hash,  8,  4) .
			'-' .
			substr($hash, 12,  4) .
			'-' .
			substr($hash, 16,  4) .
			'-' .
			substr($hash, 20, 12) .
			'}';
	return $guid;
}
?>