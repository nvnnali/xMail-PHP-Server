<?php
// includes
require_once "includes/constants.inc.php";
require_once XMAIL_CONF_PATH;
require_once "includes/misc.inc.php";
require_once "includes/keys.inc.php";
require_once "includes/spamfilter.inc.php";
require_once "../api/misc.inc.php";
require_once "../inc/user.php";

// You will need to change these values, most likely
mysql_connect($config["mysql.server"], $config["mysql.username"], $config["mysql.password"]) or die(mysql_error());
mysql_select_db($config["mysql.database"]) or die(mysql_error());

// Variable intake
$mode = clean($_POST['mode']);
$ip = $_SERVER['REMOTE_ADDR'];
$version = clean($_POST['version']); // Unused, for now
$now = time();
$debug = valid($_POST['debug']);
$delimeter = clean($_POST['delimeter']);

// Actual code
if($mode == "BOOK"){
	if(valid($_POST['title']) && valid($_POST['author']) && valid($_POST['lines'])){
		$title = clean($_POST['title']);
		$author = clean($_POST['author']);
		$lines = clean($_POST['lines']);
		$id = clean($_POST['id']);
		if(!valid($id)){
			$id = "-1";
		}				
		if($id != "-1"){
			mysql_query("INSERT INTO `snmail2` (`id`, `to`, `from`, `lines`) VALUES ('$id', '$title', '$author', '$lines')") or die(mysql_error());
		}else{
			$id = md5(sha1(time()."-".time()*time()));
			$send = time()+(2*60*60);
			mysql_query("INSERT INTO `snmail` (`id`, `to`, `from`, `lines`, `send`) VALUES ('$id', '$title', '$author', '$lines', '$send')") or die(mysql_error());
		}
		echo ok("added");
	}else{
		error("Invalid ".json_encode(array("title"=>valid($_POST['title']), "author"=>valid($_POST['author']), "lines"=>valid($_POST['lines']), "id"=>valid($_POST['id']))));
	}
}else if($mode == "GET_BOOKS"){
	$who = clean($_POST['who']);
	if(!valid($who)){
		error("Invalid");
	}else{
		$query = mysql_query("SELECT * FROM `snmail2` WHERE `to`='$who'") or die(mysql_error());
		if(mysql_num_rows($query)>0){
			echo json_encode(array("message" => "inbox", "status" => "OK", "username" => $who, "unread" => mysql_num_rows($query)));
			while($array = mysql_fetch_array($query)){
				$to = $array['to'];
				$from = $array['from'];
				$rawLines = $array['lines'];
				$lines = dirty($rawLines);
				$id = $array['id'];
				
				// Generate
				$mailMess = array();
				$mailMess["id"] = $id;
				$mailMess["to"] = $to;
				$mailMess["from"] = $from;
				$mailMess["lines"] = $lines;
				echo "\n".json_encode($mailMess);
			}
			mysql_query("DELETE FROM `snmail2` WHERE `to`='$who'")  or die(mysql_error());
		}else{
			echo json_encode(array("message" => "no mail", "status" => "OK"));
		}
	}
}else{
	error("Invalid");
}

function error($message){
	$array = array("status"=>"ERROR", "message"=>$message);
	echo json_encode($array);
	die();
}

function ok($message){
	$array = array("status"=>"OK", "message"=>$message);
	echo json_encode($array);
	die();
}
?>