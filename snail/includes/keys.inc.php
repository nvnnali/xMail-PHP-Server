<?php
/*
Keys functions for checking, creating, and removing keys
*/
function check_key($ip, $mode, $key, $other=null){
	// Check mode and console state for valid input
	if($mode == "INBOX" || $mode == "LOGOUT" || $mode == "SEND" || $mode == "MARK"){
		if(($mode == "SEND" || $mode == "INBOX" || $mode == "MARK") && strpos($other, 'CONSOLE@') !== false){
			return true;
		}
		// Check key
		if(!valid($key) || !valid($other) || !valid($ip)){
			die(json_encode(array("message" => "Invalid API key", "status" => "ERROR")));
		}
		// Only check key if we need to
		$query = mysql_query("SELECT id FROM `keys` WHERE `key`='$key' AND `ip`='$ip'") or die(mysql_error());
		if($other != null){
			if(valid($other)){
				$query = mysql_query("SELECT id FROM `keys` WHERE `key`='$key' AND `other`='$other' AND `ip`='$ip'") or die(mysql_error());
			}else{
				die(json_encode(array("message" => "Invalid API key", "status" => "ERROR")));
			}
		}
		if(mysql_num_rows($query)==1){
			// Valid, so do nothing
		}else{
			// Check import keys now
			$query = mysql_query("SELECT * FROM `import_keys` WHERE `key`='$key' AND `ip`='$ip'") or die(mysql_error());
			if(mysql_num_rows($query) != 1){
				die(json_encode(array("message" => "Invalid API key", "status" => "ERROR")));
			}else{
				mysql_query("DELETE FROM `import_keys` WHERE `key`='$key' AND `ip`='$ip' LIMIT 1") or die(mysql_error());
			}
		}
	}
}

function get_key($ip, $mode, $other=null){
	// Generates a key
	if(!valid($mode) || $other==null || !valid($ip)){
		return null;
	}else{
		$query = mysql_query("SELECT `key` FROM `keys` WHERE other='$other' AND `ip`='$ip'") or die(mysql_error());
		if(mysql_num_rows($query)==1){
			return mysql_result($query, 0, "key");
		}else{
			$key = sha1(sha1($other).time().md5($other).time()*time()); // Random stuff to make a key?
			while(mysql_num_rows(mysql_query("SELECT * FROM `keys` WHERE `key`='$key'"))>0){
				$key = sha1(md5($key.time()).sha1($other).time().md5($other).time()*time()); // Regen a new key, if needed
			}
			$now = time();
			$validation = 15*60;
			$expire = $now + $validation;
			mysql_query("INSERT INTO `keys` (`key`, `mode`, other, `ip`, `expire`) VALUES ('$key', '$mode', '$other', '$ip', '$expire')") or die(mysql_error());
			return $key;
		}
	}
}

function destroy_key($ip, $mode, $other=null){
	// Removes a key
	if(valid($mode) && $other!=null && valid($ip)){
		mysql_query("DELETE FROM `keys` WHERE other='$other' AND `ip`='$ip' LIMIT 1") or die(mysql_error());
	}
}

function userExists($username){
	if(strpos($username, 'CONSOLE@') !== false){
		return true;
	}
	return mysql_num_rows(mysql_query("SELECT id FROM users WHERE username='$username'")) > 0;
}
?>