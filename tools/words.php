<?php
// includes
require_once "includes/constants.inc.php";
require_once XMAIL_CONF_PATH;
require_once "includes/misc.inc.php";
require_once "includes/keys.inc.php";
require_once "../api/misc.inc.php";

// You will need to change these values, most likely
mysql_connect($config["mysql.server"], $config["mysql.username"], $config["mysql.password"]) or die(mysql_error());
mysql_select_db($config["mysql.database"]) or die(mysql_error());

$ip = $_SERVER['REMOTE_ADDR'];
$useIp = false;

if(valid($_REQUEST['ip'])){
	$ip = clean($_REQUEST['ip']);
	$useIp = true;
}

$words = array();
$query = null;
$title = "UNKNOWN";
if($useIp){
	$query = mysql_query("SELECT `message` FROM `mail` WHERE `sent_from`='$ip'");
}else{
	$query = mysql_query("SELECT `message` FROM `mail`");
}
if(mysql_num_rows($query)>0){
	while($a = mysql_fetch_array($query)){
		$word = explode(" ", cClean(dirty($a['message'])));
		foreach($word as $w){
			$w = strtolower(str_replace(" ", "", $w));
			if(strlen($w)>0){
				$words[$w]++;
			}
		}
	}
	if($useIp){
		$title = "<h1>Word use for $ip (".mysql_num_rows($query)." messages, ".count($words)." words)</h1>";
	}else{
		$title = "<h1>Word use for GLOBAL (".mysql_num_rows($query)." messages, ".count($words)." words)</h1>";
	}
	echo $title;
	arsort($words);
	foreach($words as $key => $val){
		echo "<pre>$key = $val</pre>";
	}
}

function cClean($input){
	$input = preg_replace("/[0-9]/", "", $input);
	$input = preg_replace("/[\n\r]/", "", $input); 
	$input = preg_replace("/[^a-zA-Z 0-9]+/", "", $input);
	return $input;
}
?>